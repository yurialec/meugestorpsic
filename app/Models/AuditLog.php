<?php

namespace App\Models;

use App\Enums\AuditEventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use RuntimeException;

class AuditLog extends Model
{
    protected $fillable = [
        'ip_address',
        'user_email',
        'user_cpf',
        'user_cpf_hash',
        'patient_cpf',
        'patient_cpf_hash',
        'event_type',
        'occurred_at',
        'metadata',
    ];

    protected $casts = [
        'user_cpf' => 'encrypted',
        'patient_cpf' => 'encrypted',
        'event_type' => AuditEventType::class,
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(function (): bool {
            throw new RuntimeException('Audit logs are immutable and cannot be updated.');
        });

        static::deleting(function (): bool {
            throw new RuntimeException('Audit logs are immutable and cannot be deleted.');
        });
    }

    public function scopeByDateRange(Builder $query, Carbon|string $start, Carbon|string $end): Builder
    {
        return $query->whereBetween('occurred_at', [
            Carbon::parse($start)->startOfDay(),
            Carbon::parse($end)->endOfDay(),
        ]);
    }

    public function scopeByUser(Builder $query, string $emailOrCpf): Builder
    {
        $normalizedCpf = preg_replace('/\D+/', '', $emailOrCpf);

        return $query->where(function (Builder $query) use ($emailOrCpf, $normalizedCpf) {
            $query->where('user_email', $emailOrCpf);

            if ($normalizedCpf !== '') {
                $query->orWhere('user_cpf_hash', self::cpfHash($normalizedCpf));
            }
        });
    }

    public function scopeByEventType(Builder $query, AuditEventType|string $eventType): Builder
    {
        return $query->where('event_type', $eventType instanceof AuditEventType ? $eventType->value : $eventType);
    }

    public static function cpfHash(?string $cpf): ?string
    {
        $normalizedCpf = preg_replace('/\D+/', '', (string) $cpf);

        if ($normalizedCpf === '') {
            return null;
        }

        return hash_hmac('sha256', $normalizedCpf, (string) config('audit.cpf_hash_salt'));
    }
}
