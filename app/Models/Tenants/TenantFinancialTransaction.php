<?php

namespace App\Models\Tenants;

use App\Models\Admin\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TenantFinancialTransaction extends BaseTenantModel
{
    use HasFactory;

    protected $table = 'tenant_financial_transactions';

    const STATUS_OPEN = 'open';
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FREE = 'free';

    protected $fillable = [
        'tenant_id',
        'patient_id',
        'consultation_id',
        'amount',
        'payment_method_id',
        'status',
        'paid_at',
        'description',
        'discount',
    ];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:00',
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function installments()
    {
        return $this->hasMany(TenantFinancialInstallment::class, 'financial_transaction_id');
    }
}
