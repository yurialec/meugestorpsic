<?php

namespace App\Models\Admin;

use App\Models\Tenants\Tenant;
use Illuminate\Database\Eloquent\Model;

class PaymentIntent extends Model
{
    protected $table = 'payment_intents';

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'plan_id',
        'payment_id',
        'status',
        'gateway_order_id',
        'charge_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
