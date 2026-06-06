<?php

namespace App\Services;

use App\Models\Admin\Payment;
use App\Models\Admin\PaymentInstallment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagSeguroService
{
    protected string $baseUrl;
    protected string $token;
    protected string $environment;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('pagseguro.base_url'), '/');
        $this->token = $this->normalizeToken((string) config('pagseguro.token'));
        $this->environment = $this->resolveEnvironment();

        if ($this->baseUrl === '') {
            throw new \InvalidArgumentException('PAGSEGURO_BASE_URL nao configurada.');
        }

        if ($this->token === '') {
            throw new \InvalidArgumentException('PAGSEGURO_TOKEN nao configurado.');
        }
    }

    private function http()
    {
        $http = Http::withHeaders($this->authHeaders())
            ->acceptJson()
            ->contentType('application/json');

        if (app()->environment('local')) {
            $http = $http->withoutVerifying();
        }

        return $http;
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    private function normalizeToken(string $token): string
    {
        $token = trim($token, " \t\n\r\0\x0B\"'");

        if (stripos($token, 'Bearer ') === 0) {
            return trim(substr($token, 7));
        }

        return $token;
    }

    private function resolveEnvironment(): string
    {
        if (str_contains($this->baseUrl, 'sandbox')) {
            return 'sandbox';
        }

        return 'production';
    }

    private function request(string $method, string $uri, array $payload = [])
    {
        $options = [];

        if (!empty($payload)) {
            $options['json'] = $payload;
        }

        return $this->http()
            ->baseUrl($this->baseUrl)
            ->send($method, $uri, $options);
    }

    private function logFailedResponse(string $operation, $response, ?string $resourceId = null): void
    {
        Log::error("PagSeguro {$operation} error", [
            'status' => $response->status(),
            'body' => $response->json(),
            'base_url' => $this->baseUrl,
            'environment' => $this->environment,
            'sandbox_configured' => filter_var(config('pagseguro.sandbox'), FILTER_VALIDATE_BOOLEAN),
            'resource_id' => $resourceId,
            'authorization_scheme' => 'Bearer',
            'token_configured' => $this->token !== '',
            'token_sha256_prefix' => $this->token !== '' ? substr(hash('sha256', $this->token), 0, 12) : null,
        ]);
    }

    private function extractErrorMessage($response, string $fallback): string
    {
        $body = $response->json() ?? [];

        return $body['error_messages'][0]['description']
            ?? $body['message']
            ?? $fallback;
    }

    private function notificationUrls(): ?array
    {
        $webhookUrl = config('pagseguro.webhook_url');

        if (empty($webhookUrl)) {
            return null;
        }

        return [$webhookUrl];
    }

    public function createPix(array $data)
    {
        $payload = [
            'reference_id' => $data['reference_id'],
            'customer' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'tax_id' => $data['tax_id'],
            ],
            'items' => [
                [
                    'reference_id' => 'item-' . $data['reference_id'],
                    'name' => $data['description'],
                    'quantity' => 1,
                    'unit_amount' => (int) $data['amount'],
                ]
            ],
            'qr_codes' => [
                [
                    'amount' => [
                        'value' => (int) $data['amount']
                    ],
                    'expiration_date' => now()->addMinutes(30)->toIso8601String()
                ]
            ]
        ];

        if ($notificationUrls = $this->notificationUrls()) {
            $payload['notification_urls'] = $notificationUrls;
        }

        $response = $this->request('POST', '/orders', $payload);

        if (!$response->successful()) {
            $this->logFailedResponse('PIX', $response);
            throw new \Exception($this->extractErrorMessage($response, 'Erro ao gerar PIX'));
        }

        $order = $response->json();

        if (!isset($order['qr_codes'])) {
            Log::error('PagSeguro PIX invalid response', ['body' => $order]);
            throw new \Exception('Resposta invalida ao gerar PIX');
        }

        return $order;
    }

    public function createCardCharge(array $data): array
    {
        $payload = [
            'reference_id' => $data['reference_id'],
            'customer' => [
                'name'   => $data['holder_name'],
                'email'  => $data['email'],
                'tax_id' => $data['tax_id'],
            ],
            'items' => [
                [
                    'reference_id' => $data['reference_id'],
                    'name'         => $data['description'],
                    'quantity'     => 1,
                    'unit_amount'  => $data['amount_cents'],
                ]
            ],
            'charges' => [
                [
                    'reference_id'   => $data['reference_id'],
                    'description'    => $data['description'],
                    'amount'         => [
                        'value'    => $data['amount_cents'],
                        'currency' => 'BRL',
                    ],
                    'payment_method' => [
                        'type'         => 'CREDIT_CARD',
                        'installments' => $data['installments'] ?? 1,
                        'capture'      => true,
                        'card'         => [
                            'encrypted' => $data['card_encrypted'],
                            'holder'    => ['name' => $data['holder_name']],
                            'store'     => false,
                        ],
                    ],
                ]
            ],
        ];

        if ($notificationUrls = $this->notificationUrls()) {
            $payload['notification_urls'] = $notificationUrls;
        }

        $response = $this->request('POST', '/orders', $payload);

        if ($response->failed()) {
            $this->logFailedResponse('Card', $response);
            throw new \Exception($this->extractErrorMessage($response, 'Erro no cartao'));
        }

        return $response->json();
    }

    public function createBoletoCharge(array $data): array
    {
        $payload = [
            'reference_id' => $data['reference_id'],
            'customer' => [
                'name' => $data['customer_name'],
                'email' => $data['customer_email'],
                'tax_id' => $data['customer_tax_id'],
                'phones' => $data['phones'] ?? [],
            ],
            'items' => [
                [
                    'reference_id' => $data['reference_id'],
                    'name' => $data['description'],
                    'quantity' => 1,
                    'unit_amount' => $data['amount_cents'],
                ]
            ],
            'charges' => [
                [
                    'reference_id' => $data['reference_id'],
                    'description' => $data['description'],
                    'amount' => [
                        'value' => $data['amount_cents'],
                        'currency' => 'BRL',
                    ],
                    'payment_method' => [
                        'type' => 'BOLETO',
                        'boleto' => [
                            'template' => 'COBRANCA',
                            'due_date' => $data['due_date'],
                            'days_until_expiration' => (string) $data['days_until_expiration'],
                            'holder' => [
                                'name' => $data['holder_name'],
                                'tax_id' => $data['holder_tax_id'],
                                'email' => $data['holder_email'],
                                'address' => [
                                    'street' => $data['address']['street'],
                                    'number' => $data['address']['number'],
                                    'complement' => $data['address']['complement'] ?? null,
                                    'locality' => $data['address']['locality'],
                                    'city' => $data['address']['city'],
                                    'region' => $data['address']['region_code'],
                                    'region_code' => $data['address']['region_code'],
                                    'country' => $data['address']['country'],
                                    'postal_code' => $data['address']['postal_code'],
                                ],
                            ],
                            'instruction_lines' => [
                                'line_1' => $data['instruction_line_1'],
                                'line_2' => $data['instruction_line_2'],
                            ],
                        ],
                    ],
                ]
            ],
        ];

        if ($notificationUrls = $this->notificationUrls()) {
            $payload['notification_urls'] = $notificationUrls;
        }

        $response = $this->request('POST', '/orders', $payload);

        if ($response->failed()) {
            $this->logFailedResponse('Boleto', $response);
            throw new \Exception($this->extractErrorMessage($response, 'Erro ao gerar boleto'));
        }

        return $response->json();
    }

    public function getOrder(string $orderId): array
    {
        $response = $this->request('GET', "/orders/{$orderId}");

        if ($response->failed()) {
            $this->logFailedResponse('order lookup', $response, $orderId);
            throw new \Exception($this->extractErrorMessage($response, 'Erro ao consultar pagamento'));
        }

        return $response->json();
    }

    public function generateInstallmentRecords(
        Payment $payment,
        float $totalAmount,
        int $installmentsCount,
        ?string $firstChargeId = null
    ): void {
        $payment->loadMissing('subscription');

        $baseAmount = floor(($totalAmount / $installmentsCount) * 100) / 100;
        $remainder = round($totalAmount - ($baseAmount * $installmentsCount), 2);
        $isPaid = strtolower((string) $payment->status) === 'paid';

        $startDate = $payment->subscription->current_period_start
            ? Carbon::parse($payment->subscription->current_period_start)
            : now();

        for ($i = 1; $i <= $installmentsCount; $i++) {
            $isFirst = $i === 1;

            $amount = $baseAmount;
            if ($i === $installmentsCount) {
                $amount += $remainder;
            }

            $installment = new PaymentInstallment([
                'payment_id' => $payment->id,
                'number' => $i,
                'amount' => $amount,
                'interest_amount' => 0.00,
                'total_amount' => $amount,
                'due_date' => $startDate->copy()->addMonths($i - 1),
                'paid_at' => $isFirst && $isPaid ? now() : null,
                'pagseguro_charge_id' => $isFirst ? $firstChargeId : null,
                'status' => $isFirst && $isPaid ? 'paid' : 'pending',
            ]);

            try {
                $installment->save();
                Log::debug("Parcela {$i}/{$installmentsCount} salva", ['id' => $installment->id]);
            } catch (\Throwable $e) {
                Log::error("Falha na parcela {$i}: " . $e->getMessage());
                throw $e;
            }
        }
    }
}
