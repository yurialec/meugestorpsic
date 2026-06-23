<?php

namespace App\Repositories\Tenants;

use App\Http\Traits\TraitPlan;
use App\Models\Admin\Payment;
use App\Models\Admin\Plan;
use App\Models\Tenants\PlanFeature;
use App\Models\Tenants\Subscription;
use App\Models\Tenants\Tenant;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionRepository
{
    protected $tenant;
    protected $subscription;
    protected $payment;

    public function __construct(Tenant $tenant, Subscription $subscription, Payment $payment)
    {
        $this->tenant = $tenant;
        $this->subscription = $subscription;
        $this->payment = $payment;
    }

    public function find()
    {
        try {
            $tenant = $this->tenant
                ->with([
                    'client',
                    'subscription.plan',
                    'address'
                ])
                ->where('domain', session('tenant_domain'))
                ->firstOrFail();
                
            $payment = $this->payment->with(['installments', 'plan', 'paymentIntent', 'paymentBoleto'])
                ->where('subscription_id', $tenant->subscription->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $endDate = Carbon::parse($tenant['subscription']['current_period_end']);
            $isExpired = $endDate->isPast();

            $tenant['is_expired'] = $isExpired;

            return [
                'tenant'   => $tenant,
                'client'   => $tenant->client,
                'plan'     => $tenant->subscription->plan,
                'payments' => $payment,
            ];
        } catch (Exception $err) {
            Log::error('Erro ao recuperar inscrição', [$err->getMessage()]);
            throw new Exception($err->getMessage());
        }
    }
}
