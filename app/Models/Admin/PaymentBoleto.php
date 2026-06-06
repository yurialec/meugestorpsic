<?php

namespace App\Models\Admin;

use App\Models\Tenants\Tenant;
use Illuminate\Database\Eloquent\Model;

class PaymentBoleto extends Model
{
    protected $table = 'payment_boletos';

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'payment_id',
        'plan_id',
        'status',
        'amount',
        'due_date',
        'issuance_date',
        'paid_at',
        'barcode',
        'digitable_line',
        'boleto_url',
        'external_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
        'issuance_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
