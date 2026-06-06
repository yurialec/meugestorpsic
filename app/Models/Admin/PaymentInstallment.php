<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;

    protected $table = 'payment_installments';

    protected $fillable = [
        'payment_id',
        'number',
        'amount',
        'interest_amount',
        'total_amount',
        'due_date',
        'paid_at',
        'pagseguro_charge_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending'
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->whereDate('due_date', '<', now());
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->total_amount)) {
                $model->total_amount = $model->amount + $model->interest_amount;
            }
        });
    }
}
