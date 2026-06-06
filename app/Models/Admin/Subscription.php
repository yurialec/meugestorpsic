<?php

namespace App\Models\Admin;

use App\Models\Tenants\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'installments_count',
        'status',
        'pagseguro_subscription_id',
        'started_at',
        'current_period_start',
        'current_period_end',
        'payment_gateway_charge_id',
        'payment_method',
        'amount_paid',
    ];

    const ACTIVE = 'active';
    const PAST_DUE = 'past_due';
    const CANCELED = 'canceled';
    const TRIAL = 'trial';

    protected $casts = [
        'started_at'           => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end'   => 'datetime',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    public function paymentIntents()
    {
        return $this->hasMany(PaymentIntent::class, 'subscription_id');
    }

    public function paymentBoletos()
    {
        return $this->hasMany(PaymentBoleto::class, 'subscription_id');
    }

    public function is_expired()
    {
        return $this->current_period_end && $this->current_period_end->isPast();
    }
}
