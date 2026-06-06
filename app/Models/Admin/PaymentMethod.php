<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_method';

    protected $fillable = ['name', 'label'];

    public static function freeOrPending(string $value): int|null
    {
        if (!in_array($value, ['free', 'pending'], true)) {
            return null;
        }
        $status = self::select('id')
            ->where('name', $value)
            ->first();

        return $status?->id; // Evita erro fatal se a seed de pending/free ainda nao existir.
    }
}
