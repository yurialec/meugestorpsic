<?php

namespace App\Http\Controllers;

use App\Models\Admin\Payment;
use App\Models\Admin\PaymentBoleto;
use App\Models\Admin\PaymentIntent;
use App\Models\Admin\Plan;
use App\Models\Admin\Subscription;
use App\Models\Admin\WebhookLog;
use App\Models\Tenants\Tenant;
use App\Services\PagSeguroService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected $service;

    public function __construct(PagSeguroService $service)
    {
        $this->service = $service;
    }

    public function generatePix(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'tenant_id' => 'nullable|integer',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $tenant = $this->resolveTenantContext($request);
        $subscription = $tenant->subscription;
        $this->validateTenantPixData($tenant);

        try {
            $referenceId = 'PIX-' . $tenant->id . '-' . strtoupper(Str::random(8));
            $taxId = preg_replace('/\D+/', '', (string) $tenant->client->cpf);

            $order = $this->service->createPix([
                'reference_id' => $referenceId,
                'name' => $tenant->client->name,
                'email' => $tenant->client->email,
                'tax_id' => $taxId,
                'description' => "Assinatura {$plan->name}",
                'amount' => (int) round($plan->price * 100),
            ]);

            if (!isset($order['qr_codes'][0])) {
                return response()->json(['message' => 'Nao foi possivel gerar o PIX'], 500);
            }

            $orderId = data_get($order, 'id');
            $chargeId = data_get($order, 'charges.0.id');
            $gatewayStatus = $this->extractGatewayStatus($order);
            $paymentStatus = $this->normalizePaymentStatus($gatewayStatus);
            $intentStatus = $this->normalizeIntentStatus($gatewayStatus);
            $pixDetails = $this->extractPixDetailsFromOrder($order);

            DB::beginTransaction();

            $payment = Payment::create([
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'pagseguro_transaction_id' => $orderId,
                'amount' => $plan->price,
                'currency' => 'BRL',
                'status' => $paymentStatus,
                'payment_method' => 'pix',
                'paid_at' => $paymentStatus === 'paid' ? now() : null,
                'installments_count' => 1,
            ]);

            $paymentIntent = PaymentIntent::create([
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
                'status' => $intentStatus,
                'gateway_order_id' => $orderId,
                'charge_id' => $chargeId,
                'amount' => $plan->price,
            ]);

            if ($paymentStatus === 'paid') {
                $this->activateSubscriptionFromPayment(
                    $subscription,
                    $payment,
                    $chargeId ?? $orderId
                );
            } else {
                $this->markSubscriptionAsPendingPayment($subscription);
            }

            $this->service->generateInstallmentRecords(
                payment: $payment,
                totalAmount: $payment->amount,
                installmentsCount: 1,
                firstChargeId: $chargeId ?? $orderId
            );

            DB::commit();

            return response()->json([
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $orderId,
                'charge_id' => $chargeId,
                'status' => $paymentStatus,
                'pix' => $pixDetails,
            ]);
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('generatePix failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tenant_id' => $tenant->id ?? null,
                'plan_id' => $plan->id ?? null,
            ]);

            return response()->json([
                'message' => 'Erro ao gerar PIX: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function pixStatus(Request $request, Payment $payment)
    {
        $tenantId = (int) ($request->tenant['id'] ?? 0);

        if ((int) $payment->subscription?->tenant_id !== $tenantId) {
            return response()->json(['message' => 'Pagamento nao encontrado'], 404);
        }

        if ($payment->status !== 'paid' && $payment->pagseguro_transaction_id) {
            try {
                $order = $this->service->getOrder($payment->pagseguro_transaction_id);
                $this->syncPaymentWithGatewayOrder($payment, $order);
                $payment->refresh();
            } catch (\Throwable $e) {
                Log::warning('pixStatus lookup failed', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'paid_at' => $payment->paid_at,
        ]);
    }

    public function webhook(Request $request)
    {
        $configuredToken = config('pagseguro.webhook_token');
        $receivedToken = (string) $request->query('token', $request->header('X-Webhook-Token', ''));

        if (!empty($configuredToken) && !hash_equals($configuredToken, $receivedToken)) {
            Log::warning('PagBank webhook unauthorized', ['ip' => $request->ip()]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        $eventType = $this->resolveWebhookEventType($payload);

        $log = WebhookLog::create([
            'event_type' => $eventType,
            'payload_json' => $payload,
            'processed' => false,
            'created_at' => now(),
        ]);

        try {
            $paymentIntent = $this->findPaymentIntentFromGatewayPayload($payload);
            $paymentBoleto = $this->findPaymentBoletoFromGatewayPayload($payload);
            $payment = $this->findPaymentFromGatewayPayload($payload);

            if (!$payment && $paymentIntent?->payment) {
                $payment = $paymentIntent->payment;
            }

            if (!$payment && $paymentBoleto?->payment) {
                $payment = $paymentBoleto->payment;
            }

            if (!$payment && !$paymentIntent && !$paymentBoleto) {
                Log::warning('PagBank webhook payment not found', [
                    'log_id' => $log->id,
                    'order_id' => data_get($payload, 'id'),
                    'charge_id' => data_get($payload, 'charges.0.id'),
                ]);

                return response()->json([
                    'received' => true,
                    'processed' => false,
                ], 202);
            }

            if ($payment) {
                $this->syncPaymentWithGatewayOrder($payment, $payload, $paymentIntent, $paymentBoleto);
            } elseif ($paymentBoleto) {
                $this->syncPaymentBoleto($paymentBoleto, $payload, $paymentIntent);
            } elseif ($paymentIntent) {
                $this->syncPaymentIntent($paymentIntent, $payload);
            }

            $log->processed = true;
            $log->save();

            return response()->json([
                'received' => true,
                'processed' => true,
            ]);
        } catch (\Throwable $e) {
            Log::error('PagBank webhook processing failed', [
                'log_id' => $log->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'received' => true,
                'processed' => false,
            ], 202);
        }
    }

    public function payWithCard(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'card_encrypted' => 'required|string',
            'holder_name' => 'required|string',
            'installments' => 'nullable|integer|min:1|max:12',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $tenant = $request->tenant;
        $subscription = $this->resolveSubscription($request);

        if (!isset($tenant['id'])) {
            return response()->json(['message' => 'Tenant invalido'], 422);
        }

        $refId = 'SUB-' . $tenant['id'] . '-' . strtoupper(Str::random(8));
        $installmentsCount = max(1, min(12, (int) ($request->installments ?? 1)));

        $minInstallmentValue = 5.00;
        if (($plan->price / $installmentsCount) < $minInstallmentValue) {
            return response()->json([
                'message' => "Minimo de R$ {$minInstallmentValue} por parcela. Reduza o numero de parcelas."
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order = $this->service->createCardCharge([
                'reference_id' => $refId,
                'description' => "Assinatura {$plan->name} ({$installmentsCount}x)",
                'amount_cents' => (int) round($plan->price * 100),
                'card_encrypted' => $request->card_encrypted,
                'holder_name' => $request->holder_name,
                'installments' => $installmentsCount,
                'email' => $tenant['client']['email'],
                'tax_id' => $tenant['client']['cpf'],
            ]);

            $gatewayStatus = $this->extractGatewayStatus($order);
            $paymentStatus = $this->normalizePaymentStatus($gatewayStatus);
            $chargeId = $order['charges'][0]['id'] ?? null;
            $amountPaid = $order['charges'][0]['amount']['value'] ?? 0;

            $payment = Payment::create([
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'pagseguro_transaction_id' => $chargeId,
                'amount' => $amountPaid / 100,
                'currency' => 'BRL',
                'status' => $paymentStatus,
                'payment_method' => 'credit_card',
                'paid_at' => $paymentStatus === 'paid' ? now() : null,
                'installments_count' => $installmentsCount,
            ]);

            if ($paymentStatus === 'paid') {
                $this->activateSubscriptionFromPayment($subscription, $payment, $chargeId);
            } else {
                $subscription->status = Subscription::PAST_DUE;
                $subscription->save();
            }

            $this->service->generateInstallmentRecords(
                payment: $payment,
                totalAmount: $payment->amount,
                installmentsCount: $payment->installments_count ?? $installmentsCount,
                firstChargeId: $chargeId
            );

            DB::commit();

            return response()->json([
                'status' => $paymentStatus,
                'charge_id' => $chargeId,
                'installments' => $installmentsCount,
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Subscription not found for tenant', ['tenant_id' => $tenant['id']]);

            return response()->json(['message' => 'Assinatura nao encontrada para este tenant.'], 404);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('payWithCard failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tenant_id' => $tenant['id'] ?? null,
            ]);

            return response()->json(['message' => 'Erro ao processar pagamento: ' . $e->getMessage()], 422);
        }
    }

    public function payWithBoleto(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'tenant_id' => 'nullable|integer',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $tenant = $this->resolveTenantContext($request);
        $subscription = $tenant->subscription;
        $this->validateTenantBillingData($tenant);
        $this->expireStalePendingBoletos($tenant->id);

        $existingBoleto = $this->findLatestPendingBoleto($tenant->id);

        if ($existingBoleto) {
            if ((int) $existingBoleto->plan_id === (int) $plan->id) {
                return response()->json([
                    'status' => 'pending',
                    'reused' => true,
                    'payment_id' => $existingBoleto->payment_id,
                    'payment_intent_id' => $existingBoleto->payment?->paymentIntent?->id,
                    'charge_id' => $existingBoleto->payment?->pagseguro_transaction_id,
                    'payment_boleto_id' => $existingBoleto->id,
                    'boleto' => $this->formatBoletoResponse($existingBoleto),
                ]);
            }

            $this->cancelPendingBoleto($existingBoleto);
        }

        if ((float) $plan->price < 0.20) {
            return response()->json([
                'message' => 'O valor minimo para boleto no PagBank e de R$ 0,20.'
            ], 422);
        }

        $phoneDigits = preg_replace('/\D+/', '', (string) $tenant->client->phone);
        $phones = [];

        if (!empty($phoneDigits) && strlen($phoneDigits) >= 10) {
            $phones[] = [
                'country' => '55',
                'area' => substr($phoneDigits, 0, 2),
                'number' => substr($phoneDigits, 2),
                'type' => 'MOBILE',
            ];
        }

        $postalCode = preg_replace('/\D+/', '', (string) $tenant->address->postal_code);
        $taxId = preg_replace('/\D+/', '', (string) $tenant->client->cpf);

        DB::beginTransaction();

        try {
            $issuanceDate = now();
            $dueDate = $issuanceDate->copy()->addDays(3);
            $referenceId = 'BOL-' . $tenant->id . '-' . strtoupper(Str::random(8));

            $order = $this->service->createBoletoCharge([
                'reference_id' => $referenceId,
                'description' => "Assinatura {$plan->name} - boleto",
                'amount_cents' => (int) round($plan->price * 100),
                'customer_name' => $tenant->client->name,
                'customer_email' => $tenant->client->email,
                'customer_tax_id' => $taxId,
                'holder_name' => $tenant->client->name,
                'holder_email' => $tenant->client->email,
                'holder_tax_id' => $taxId,
                'phones' => $phones,
                'due_date' => $dueDate->format('Y-m-d'),
                'days_until_expiration' => 3,
                'instruction_line_1' => 'Pagamento referente a assinatura Meu Gestor Saude',
                'instruction_line_2' => 'Nao receber apos o vencimento',
                'address' => [
                    'street' => $tenant->address->street,
                    'number' => $tenant->address->number,
                    'complement' => $tenant->address->complement,
                    'locality' => $tenant->address->neighborhood,
                    'city' => $tenant->address->city,
                    'region_code' => strtoupper($tenant->address->state),
                    'country' => 'BRA',
                    'postal_code' => $postalCode,
                ],
            ]);

            $chargeId = data_get($order, 'charges.0.id');
            $gatewayStatus = $this->extractGatewayStatus($order);
            $paymentStatus = $this->normalizePaymentStatus($gatewayStatus);
            $intentStatus = $this->normalizeIntentStatus($gatewayStatus);
            $boletoStatus = $this->normalizeBoletoStatus($gatewayStatus);
            $orderId = data_get($order, 'id');
            $boletoDetails = $this->extractBoletoDetailsFromOrder($order);

            $payment = Payment::create([
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'pagseguro_transaction_id' => $chargeId,
                'amount' => $plan->price,
                'currency' => 'BRL',
                'status' => $paymentStatus,
                'payment_method' => 'boleto',
                'paid_at' => $paymentStatus === 'paid' ? now() : null,
                'installments_count' => 1,
            ]);

            $paymentIntent = PaymentIntent::create([
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
                'status' => $intentStatus,
                'gateway_order_id' => $orderId,
                'charge_id' => $chargeId,
                'amount' => $plan->price,
            ]);

            $paymentBoleto = PaymentBoleto::create([
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'plan_id' => $plan->id,
                'status' => $boletoStatus,
                'amount' => $plan->price,
                'due_date' => $dueDate,
                'issuance_date' => $issuanceDate,
                'paid_at' => $paymentStatus === 'paid' ? now() : null,
                'barcode' => $boletoDetails['barcode'],
                'digitable_line' => $boletoDetails['digitable_line'],
                'boleto_url' => $boletoDetails['boleto_url'],
                'external_id' => $orderId,
            ]);

            if ($paymentStatus === 'paid') {
                $this->activateSubscriptionFromPayment($subscription, $payment, $chargeId);
            } else {
                $this->markSubscriptionAsPendingPayment($subscription);
            }

            $this->service->generateInstallmentRecords(
                payment: $payment,
                totalAmount: $payment->amount,
                installmentsCount: 1,
                firstChargeId: $chargeId
            );

            $payment->installments()
                ->where('number', 1)
                ->update([
                    'due_date' => $dueDate,
                ]);

            DB::commit();

            return response()->json([
                'status' => $paymentStatus,
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntent->id,
                'payment_boleto_id' => $paymentBoleto->id,
                'charge_id' => $chargeId,
                'boleto' => $this->formatBoletoResponse($paymentBoleto),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('payWithBoleto failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tenant_id' => $tenant->id ?? null,
            ]);

            return response()->json([
                'message' => 'Erro ao gerar boleto: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function showPendingBoleto(Request $request, Payment $payment)
    {
        $payment->loadMissing(['subscription', 'paymentIntent', 'paymentBoleto']);

        $tenantId = (int) ($request->tenant['id'] ?? 0);
        $paymentIntent = $payment->paymentIntent;
        $paymentBoleto = $payment->paymentBoleto;

        if ((int) $payment->subscription?->tenant_id !== $tenantId) {
            return response()->json(['message' => 'Pagamento nao encontrado.'], 404);
        }

        if ($payment->payment_method !== 'boleto' || !$paymentIntent || !$paymentBoleto) {
            return response()->json(['message' => 'Boleto nao encontrado para este pagamento.'], 404);
        }

        if ($this->shouldExpireBoleto($paymentBoleto)) {
            $this->expireBoleto($paymentBoleto);
            $payment->refresh()->loadMissing(['paymentIntent', 'paymentBoleto']);
            $paymentIntent = $payment->paymentIntent;
            $paymentBoleto = $payment->paymentBoleto;
        }

        if ($paymentIntent->status !== 'pending' || $paymentBoleto->status !== 'pending') {
            return response()->json(['message' => 'Este boleto nao esta mais pendente.'], 422);
        }

        if (!$paymentBoleto->external_id) {
            return response()->json(['message' => 'Nao foi possivel localizar o boleto no gateway.'], 422);
        }

        try {
            $order = $this->service->getOrder($paymentBoleto->external_id);
            $this->syncPaymentWithGatewayOrder($payment, $order, $paymentIntent, $paymentBoleto);
            $payment->refresh()->loadMissing(['paymentIntent', 'paymentBoleto']);

            if ($payment->paymentIntent?->status !== 'pending' || $payment->paymentBoleto?->status !== 'pending') {
                return response()->json([
                    'message' => 'Este boleto nao esta mais pendente.',
                    'status' => $payment->paymentBoleto?->status ?? $payment->paymentIntent?->status,
                ], 422);
            }

            return response()->json([
                'status' => $payment->paymentBoleto?->status,
                'boleto' => $this->formatBoletoResponse($payment->paymentBoleto),
            ]);
        } catch (\Throwable $e) {
            Log::error('showPendingBoleto failed', [
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntent->id,
                'payment_boleto_id' => $paymentBoleto->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao consultar boleto pendente: ' . $e->getMessage(),
            ], 422);
        }
    }

    private function resolveSubscription(Request $request): Subscription
    {
        return Subscription::findOrFail($request->tenant['subscription']['id']);
    }

    private function resolveWebhookEventType(array $payload): string
    {
        $method = data_get($payload, 'charges.0.payment_method.type');
        $status = data_get($payload, 'charges.0.status') ?? data_get($payload, 'status') ?? 'unknown';

        return trim("{$method}:{$status}", ':');
    }

    private function findPaymentFromGatewayPayload(array $payload): ?Payment
    {
        $chargeId = data_get($payload, 'charges.0.id');
        $orderId = data_get($payload, 'id');

        $query = Payment::with(['subscription', 'plan', 'installments', 'paymentIntent', 'paymentBoleto']);

        if ($chargeId) {
            $query->where('pagseguro_transaction_id', $chargeId)
                ->orWhereHas('installments', function ($installments) use ($chargeId) {
                    $installments->where('pagseguro_charge_id', $chargeId);
                });
        } elseif ($orderId) {
            $query->where('pagseguro_transaction_id', $orderId);
        } else {
            return null;
        }

        return $query->first();
    }

    private function findPaymentIntentFromGatewayPayload(array $payload): ?PaymentIntent
    {
        $chargeId = data_get($payload, 'charges.0.id');
        $orderId = data_get($payload, 'id');

        return PaymentIntent::with(['payment.subscription', 'subscription', 'plan'])
            ->when($chargeId, fn($query) => $query->where('charge_id', $chargeId))
            ->when(!$chargeId && $orderId, fn($query) => $query->where('gateway_order_id', $orderId))
            ->first();
    }

    private function findPaymentBoletoFromGatewayPayload(array $payload): ?PaymentBoleto
    {
        $chargeId = data_get($payload, 'charges.0.id');
        $orderId = data_get($payload, 'id');

        return PaymentBoleto::with(['payment.paymentIntent', 'subscription', 'plan'])
            ->when($orderId, fn($query) => $query->where('external_id', $orderId))
            ->when(!$orderId && $chargeId, function ($query) use ($chargeId) {
                $query->whereHas('payment.paymentIntent', function ($paymentIntentQuery) use ($chargeId) {
                    $paymentIntentQuery->where('charge_id', $chargeId);
                });
            })
            ->first();
    }

    private function extractGatewayStatus(array $payload): string
    {
        return strtoupper((string) (
            data_get($payload, 'charges.0.status')
            ?? data_get($payload, 'qr_codes.0.status')
            ?? data_get($payload, 'status')
            ?? 'PENDING'
        ));
    }

    private function normalizePaymentStatus(?string $gatewayStatus): string
    {
        return match (strtoupper((string) $gatewayStatus)) {
            'PAID', 'COMPLETED', 'AUTHORIZED' => 'paid',
            'DECLINED', 'FAILED', 'CANCELED', 'CANCELLED', 'EXPIRED' => 'failed',
            'REFUNDED', 'CHARGED_BACK' => 'refunded',
            default => 'pending',
        };
    }

    private function normalizeIntentStatus(?string $gatewayStatus): string
    {
        return match (strtoupper((string) $gatewayStatus)) {
            'PAID', 'COMPLETED', 'AUTHORIZED' => 'paid',
            'DECLINED', 'FAILED' => 'failed',
            'REFUNDED', 'CHARGED_BACK' => 'refunded',
            'CANCELED', 'CANCELLED', 'EXPIRED' => 'canceled',
            default => 'pending',
        };
    }

    private function normalizeBoletoStatus(?string $gatewayStatus): string
    {
        return match (strtoupper((string) $gatewayStatus)) {
            'PAID', 'COMPLETED', 'AUTHORIZED' => 'paid',
            'EXPIRED' => 'expired',
            'CANCELED', 'CANCELLED', 'DECLINED', 'FAILED', 'REFUNDED', 'CHARGED_BACK' => 'cancelled',
            default => 'pending',
        };
    }

    private function activateSubscriptionFromPayment(
        Subscription $subscription,
        Payment $payment,
        ?string $chargeId = null
    ): void {
        $periodStart = now();
        $periodEnd = $payment->plan?->billing_cycle === 'annual'
            ? Carbon::now()->addYear()
            : Carbon::now()->addMonth();

        $subscription->plan_id = $payment->plan_id;
        $subscription->status = Subscription::ACTIVE;
        $subscription->payment_method = $payment->payment_method;
        $subscription->payment_gateway_charge_id = $chargeId ?? $payment->pagseguro_transaction_id;
        $subscription->amount_paid = $payment->amount;
        $subscription->installments_count = $payment->installments_count ?? 1;
        $subscription->started_at = $subscription->started_at ?? $periodStart;
        $subscription->current_period_start = $periodStart;
        $subscription->current_period_end = $periodEnd;
        $subscription->save();
    }

    private function markSubscriptionAsPendingPayment(Subscription $subscription): void
    {
        $subscription->status = Subscription::PAST_DUE;
        $subscription->save();
    }

    private function syncPaymentWithGatewayOrder(
        Payment $payment,
        array $order,
        ?PaymentIntent $paymentIntent = null,
        ?PaymentBoleto $paymentBoleto = null
    ): void {
        $gatewayStatus = $this->extractGatewayStatus($order);
        $status = $this->normalizePaymentStatus($gatewayStatus);
        $intentStatus = $this->normalizeIntentStatus($gatewayStatus);
        $boletoStatus = $this->normalizeBoletoStatus($gatewayStatus);
        $gatewayChargeId = data_get($order, 'charges.0.id');
        $gatewayOrderId = data_get($order, 'id');
        $paidAt = data_get($order, 'charges.0.paid_at');
        $amountValue = data_get($order, 'charges.0.amount.value');
        $boletoDetails = $this->extractBoletoDetailsFromOrder($order);
        $paymentIntent ??= $payment->paymentIntent;
        $paymentBoleto ??= $payment->paymentBoleto;

        DB::transaction(function () use (
            $payment,
            $paymentIntent,
            $paymentBoleto,
            $status,
            $intentStatus,
            $boletoStatus,
            $gatewayChargeId,
            $gatewayOrderId,
            $paidAt,
            $amountValue,
            $boletoDetails
        ) {
            $payment->status = $status;

            if (!is_null($amountValue)) {
                $payment->amount = ((float) $amountValue) / 100;
            }

            $payment->paid_at = $status === 'paid'
                ? ($payment->paid_at ?? ($paidAt ? Carbon::parse($paidAt) : now()))
                : null;
            $payment->save();

            if ($paymentIntent) {
                $paymentIntent->status = $intentStatus;
                $paymentIntent->gateway_order_id = $gatewayOrderId ?? $paymentIntent->gateway_order_id;
                $paymentIntent->charge_id = $gatewayChargeId ?? $paymentIntent->charge_id;

                if (!is_null($amountValue)) {
                    $paymentIntent->amount = ((float) $amountValue) / 100;
                }

                if (!$paymentIntent->payment_id) {
                    $paymentIntent->payment_id = $payment->id;
                }

                $paymentIntent->save();
            }

            if ($paymentBoleto) {
                $paymentBoleto->status = $boletoStatus;
                $paymentBoleto->external_id = $gatewayOrderId ?? $paymentBoleto->external_id;
                $paymentBoleto->paid_at = $boletoStatus === 'paid'
                    ? ($paymentBoleto->paid_at ?? ($paidAt ? Carbon::parse($paidAt) : now()))
                    : null;
                $paymentBoleto->barcode = $boletoDetails['barcode'] ?: $paymentBoleto->barcode;
                $paymentBoleto->digitable_line = $boletoDetails['digitable_line'] ?: $paymentBoleto->digitable_line;
                $paymentBoleto->boleto_url = $boletoDetails['boleto_url'] ?: $paymentBoleto->boleto_url;
                $paymentBoleto->due_date = $boletoDetails['due_date'] ?? $paymentBoleto->due_date;

                if (!is_null($amountValue)) {
                    $paymentBoleto->amount = ((float) $amountValue) / 100;
                }

                $paymentBoleto->save();
            }

            if ($status === 'paid') {
                $this->activateSubscriptionFromPayment(
                    $payment->subscription,
                    $payment,
                    $gatewayChargeId ?? $gatewayOrderId
                );

                $payment->installments()
                    ->where('number', 1)
                    ->update([
                        'status' => 'paid',
                        'paid_at' => $payment->paid_at,
                        'pagseguro_charge_id' => $gatewayChargeId ?? $gatewayOrderId,
                    ]);
            } elseif (in_array($status, ['failed', 'refunded'], true)) {
                $payment->installments()
                    ->where('number', 1)
                    ->update([
                        'status' => $status,
                    ]);

                $this->markSubscriptionAsPendingPayment($payment->subscription);
            }
        });
    }

    private function syncPaymentIntent(PaymentIntent $paymentIntent, array $order): void
    {
        $intentStatus = $this->normalizeIntentStatus($this->extractGatewayStatus($order));
        $gatewayChargeId = data_get($order, 'charges.0.id');
        $gatewayOrderId = data_get($order, 'id');
        $amountValue = data_get($order, 'charges.0.amount.value');

        DB::transaction(function () use ($paymentIntent, $intentStatus, $gatewayChargeId, $gatewayOrderId, $amountValue) {
            $paymentIntent->status = $intentStatus;
            $paymentIntent->gateway_order_id = $gatewayOrderId ?? $paymentIntent->gateway_order_id;
            $paymentIntent->charge_id = $gatewayChargeId ?? $paymentIntent->charge_id;

            if (!is_null($amountValue)) {
                $paymentIntent->amount = ((float) $amountValue) / 100;
            }

            $paymentIntent->save();

            if ($paymentIntent->subscription && $intentStatus !== 'paid') {
                $this->markSubscriptionAsPendingPayment($paymentIntent->subscription);
            }
        });
    }

    private function syncPaymentBoleto(
        PaymentBoleto $paymentBoleto,
        array $order,
        ?PaymentIntent $paymentIntent = null
    ): void {
        $gatewayStatus = $this->extractGatewayStatus($order);
        $boletoStatus = $this->normalizeBoletoStatus($gatewayStatus);
        $gatewayOrderId = data_get($order, 'id');
        $amountValue = data_get($order, 'charges.0.amount.value');
        $paidAt = data_get($order, 'charges.0.paid_at');
        $boletoDetails = $this->extractBoletoDetailsFromOrder($order);
        $paymentIntent ??= $paymentBoleto->payment?->paymentIntent;

        DB::transaction(function () use (
            $paymentBoleto,
            $paymentIntent,
            $boletoStatus,
            $gatewayOrderId,
            $amountValue,
            $paidAt,
            $boletoDetails
        ) {
            $paymentBoleto->status = $boletoStatus;
            $paymentBoleto->external_id = $gatewayOrderId ?? $paymentBoleto->external_id;
            $paymentBoleto->paid_at = $boletoStatus === 'paid'
                ? ($paymentBoleto->paid_at ?? ($paidAt ? Carbon::parse($paidAt) : now()))
                : null;
            $paymentBoleto->barcode = $boletoDetails['barcode'] ?: $paymentBoleto->barcode;
            $paymentBoleto->digitable_line = $boletoDetails['digitable_line'] ?: $paymentBoleto->digitable_line;
            $paymentBoleto->boleto_url = $boletoDetails['boleto_url'] ?: $paymentBoleto->boleto_url;
            $paymentBoleto->due_date = $boletoDetails['due_date'] ?? $paymentBoleto->due_date;

            if (!is_null($amountValue)) {
                $paymentBoleto->amount = ((float) $amountValue) / 100;
            }

            $paymentBoleto->save();

            if ($paymentIntent) {
                $paymentIntent->status = $this->mapBoletoToIntentStatus($boletoStatus);
                $paymentIntent->gateway_order_id = $gatewayOrderId ?? $paymentIntent->gateway_order_id;
                $paymentIntent->save();
            }

            if ($paymentBoleto->subscription && $boletoStatus !== 'paid') {
                $this->markSubscriptionAsPendingPayment($paymentBoleto->subscription);
            }
        });
    }

    private function expireStalePendingBoletos(int $tenantId): void
    {
        PaymentBoleto::with(['payment.paymentIntent', 'subscription'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->where('issuance_date', '<=', now()->subDays(10))
            ->get()
            ->each(fn(PaymentBoleto $boleto) => $this->expireBoleto($boleto));
    }

    private function findLatestPendingBoleto(int $tenantId): ?PaymentBoleto
    {
        return PaymentBoleto::with(['payment.paymentIntent', 'subscription'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->orderByDesc('issuance_date')
            ->first();
    }

    private function shouldExpireBoleto(PaymentBoleto $paymentBoleto): bool
    {
        return $paymentBoleto->status === 'pending'
            && $paymentBoleto->issuance_date
            && Carbon::parse($paymentBoleto->issuance_date)->lte(now()->subDays(10));
    }

    private function expireBoleto(PaymentBoleto $paymentBoleto): void
    {
        DB::transaction(function () use ($paymentBoleto) {
            $paymentBoleto->status = 'expired';
            $paymentBoleto->save();

            if ($paymentBoleto->payment?->paymentIntent) {
                $paymentBoleto->payment->paymentIntent->status = 'canceled';
                $paymentBoleto->payment->paymentIntent->save();
            }

            if ($paymentBoleto->payment) {
                $paymentBoleto->payment->status = 'failed';
                $paymentBoleto->payment->paid_at = null;
                $paymentBoleto->payment->save();

                $paymentBoleto->payment->installments()
                    ->where('number', 1)
                    ->update([
                        'status' => 'failed',
                    ]);
            }

            if ($paymentBoleto->subscription) {
                $this->markSubscriptionAsPendingPayment($paymentBoleto->subscription);
            }
        });
    }

    private function cancelPendingBoleto(PaymentBoleto $paymentBoleto): void
    {
        DB::transaction(function () use ($paymentBoleto) {
            $paymentBoleto->status = 'cancelled';
            $paymentBoleto->save();

            if ($paymentBoleto->payment?->paymentIntent) {
                $paymentBoleto->payment->paymentIntent->status = 'canceled';
                $paymentBoleto->payment->paymentIntent->save();
            }

            if ($paymentBoleto->payment) {
                $paymentBoleto->payment->status = 'failed';
                $paymentBoleto->payment->paid_at = null;
                $paymentBoleto->payment->save();

                $paymentBoleto->payment->installments()
                    ->where('number', 1)
                    ->update([
                        'status' => 'failed',
                    ]);
            }

            if ($paymentBoleto->subscription) {
                $this->markSubscriptionAsPendingPayment($paymentBoleto->subscription);
            }
        });
    }

    private function extractBoletoDetailsFromOrder(array $order): array
    {
        $charge = data_get($order, 'charges.0', []);

        return [
            'barcode' => data_get($charge, 'payment_method.boleto.barcode'),
            'digitable_line' => data_get($charge, 'payment_method.boleto.formatted_barcode'),
            'boleto_url' => data_get(
                collect(data_get($charge, 'links', []))->firstWhere('media', 'application/pdf'),
                'href'
            ),
            'due_date' => $this->parseNullableDate(data_get($charge, 'payment_method.boleto.due_date')),
        ];
    }

    private function extractPixDetailsFromOrder(array $order): array
    {
        $qrCode = data_get($order, 'qr_codes.0', []);
        $links = collect(data_get($qrCode, 'links', []))
            ->map(fn($link) => [
                'rel' => $link['rel'] ?? null,
                'media' => $link['media'] ?? null,
                'href' => trim((string) ($link['href'] ?? '')),
                'type' => $link['type'] ?? null,
            ])
            ->filter(fn($link) => $link['href'] !== '')
            ->values()
            ->all();

        $imageUrl = data_get(
            collect($links)->firstWhere('media', 'image/png'),
            'href'
        );

        $base64Url = data_get(
            collect($links)->firstWhere('media', 'text/plain'),
            'href'
        );

        return [
            'id' => data_get($qrCode, 'id'),
            'text' => data_get($qrCode, 'text'),
            'copy_paste' => data_get($qrCode, 'text'),
            'qr_code_url' => $imageUrl,
            'base64_url' => $base64Url,
            'expiration_date' => data_get($qrCode, 'expiration_date'),
            'links' => $links,
        ];
    }

    private function formatBoletoResponse(PaymentBoleto $paymentBoleto): array
    {
        return [
            'barcode' => $paymentBoleto->barcode,
            'formatted_barcode' => $paymentBoleto->digitable_line,
            'due_date' => optional($paymentBoleto->due_date)->toISOString(),
            'pdf_url' => $paymentBoleto->boleto_url,
        ];
    }

    private function mapBoletoToIntentStatus(string $boletoStatus): string
    {
        return match ($boletoStatus) {
            'paid' => 'paid',
            'pending' => 'pending',
            default => 'canceled',
        };
    }

    private function parseNullableDate(?string $date): ?Carbon
    {
        return $date ? Carbon::parse($date) : null;
    }

    private function resolveTenantContext(Request $request): Tenant
    {
        $tenantId = (int) (
            $request->input('tenant_id')
            ?? $request->input('tenant.id')
            ?? data_get($request->tenant, 'id')
        );

        if ($tenantId <= 0) {
            throw ValidationException::withMessages([
                'tenant_id' => 'Tenant invalido.',
            ]);
        }

        return Tenant::with(['client', 'address', 'subscription.plan', 'paymentIntents', 'paymentBoletos'])->findOrFail($tenantId);
    }

    private function validateTenantBillingData(Tenant $tenant): void
    {
        if (!$tenant->subscription) {
            throw ValidationException::withMessages([
                'tenant' => 'Assinatura do tenant nao encontrada.',
            ]);
        }

        if (!$tenant->client) {
            throw ValidationException::withMessages([
                'tenant' => 'Cliente do tenant nao encontrado.',
            ]);
        }

        if (!$tenant->address) {
            throw ValidationException::withMessages([
                'tenant' => 'Endereco do tenant nao encontrado.',
            ]);
        }

        $missing = [];

        foreach (
            [
                'name' => 'nome',
                'email' => 'e-mail',
                'cpf' => 'CPF',
                'phone' => 'telefone',
            ] as $field => $label
        ) {
            if (!$this->hasValue(data_get($tenant->client, $field))) {
                $missing[] = $label;
            }
        }

        foreach (
            [
                'street' => 'rua',
                'number' => 'numero',
                'complement' => 'complemento',
                'neighborhood' => 'bairro',
                'city' => 'cidade',
                'state' => 'UF',
                'postal_code' => 'CEP',
            ] as $field => $label
        ) {
            if (!$this->hasValue(data_get($tenant->address, $field))) {
                $missing[] = $label;
            }
        }

        if (!empty($missing)) {
            throw ValidationException::withMessages([
                'tenant' => 'Complete os dados obrigatorios do perfil antes de emitir o boleto: ' . implode(', ', $missing) . '.',
            ]);
        }
    }

    private function validateTenantPixData(Tenant $tenant): void
    {
        if (!$tenant->subscription) {
            throw ValidationException::withMessages([
                'tenant' => 'Assinatura do tenant nao encontrada.',
            ]);
        }

        if (!$tenant->client) {
            throw ValidationException::withMessages([
                'tenant' => 'Cliente do tenant nao encontrado.',
            ]);
        }

        $missing = [];

        foreach (
            [
                'name' => 'nome',
                'email' => 'e-mail',
                'cpf' => 'CPF',
            ] as $field => $label
        ) {
            if (!$this->hasValue(data_get($tenant->client, $field))) {
                $missing[] = $label;
            }
        }

        if (!empty($missing)) {
            throw ValidationException::withMessages([
                'tenant' => 'Complete os dados obrigatorios do perfil antes de gerar o Pix: ' . implode(', ', $missing) . '.',
            ]);
        }
    }

    private function hasValue(mixed $value): bool
    {
        return trim((string) $value) !== '';
    }
}
