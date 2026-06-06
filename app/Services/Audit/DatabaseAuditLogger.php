<?php

namespace App\Services\Audit;

use App\Contracts\Audit\AuditLogger;
use App\Enums\AuditEventType;
use App\Jobs\Audit\StoreAuditLog;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DatabaseAuditLogger implements AuditLogger
{
    public function log(AuditEventType|string $eventType, array $data = [], ?Request $request = null): void
    {
        if (!config('audit.enabled')) {
            return;
        }

        $request ??= request();
        $user = $data['user'] ?? $this->currentUser();
        $eventType = $eventType instanceof AuditEventType ? $eventType : AuditEventType::from($eventType);
        $userCpf = $data['user_cpf'] ?? data_get($user, 'cpf');
        $patientCpf = $data['patient_cpf'] ?? null;

        $payload = [
            'ip_address' => $data['ip_address'] ?? $this->ipAddress($request),
            'user_email' => $data['user_email'] ?? data_get($user, 'email') ?? $request->input('email') ?? 'anonymous',
            'user_cpf' => $userCpf,
            'user_cpf_hash' => AuditLog::cpfHash($userCpf),
            'patient_cpf' => $patientCpf,
            'patient_cpf_hash' => AuditLog::cpfHash($patientCpf),
            'event_type' => $eventType->value,
            'occurred_at' => Carbon::now(config('audit.timezone')),
            'metadata' => array_filter([
                'user_agent' => $request->userAgent(),
                'tenant_id' => $data['tenant_id'] ?? session('tenant_id'),
                ...($data['metadata'] ?? []),
            ], static fn ($value) => $value !== null),
        ];

        if (config('audit.queue.enabled')) {
            StoreAuditLog::dispatch($payload)->afterCommit();

            return;
        }

        AuditLog::create($payload);
    }

    private function currentUser(): mixed
    {
        foreach (['web', 'client', 'employee'] as $guard) {
            if (auth()->guard($guard)->check()) {
                return auth()->guard($guard)->user();
            }
        }

        return auth()->user();
    }

    private function ipAddress(Request $request): string
    {
        foreach (config('audit.ip_headers', []) as $header) {
            $value = $request->header($header);

            if (!$value) {
                continue;
            }

            return trim(explode(',', $value)[0]);
        }

        return (string) $request->ip();
    }
}
