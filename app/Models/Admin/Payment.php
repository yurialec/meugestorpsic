<?php

namespace App\Models\Admin;

use App\Models\Admin\Subscription;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'subscription_id',
        'plan_id',
        'pagseguro_transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'paid_at',
        'installments_count',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function paymentIntent()
    {
        return $this->hasOne(PaymentIntent::class, 'payment_id');
    }

    public function paymentBoleto()
    {
        return $this->hasOne(PaymentBoleto::class, 'payment_id');
    }
}
