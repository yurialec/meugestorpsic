<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantFinancialInstallment extends Model
{
    use HasFactory;

    protected $table = 'tenant_financial_installments';

    protected $fillable = [
        'financial_transaction_id',
        'num',
        'amount',
        'paid_at',
    ];
}
