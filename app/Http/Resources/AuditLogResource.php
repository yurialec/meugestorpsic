<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use BackedEnum;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,
            'user_email' => $this->user_email,
            'user_cpf' => $request->boolean('include_cpf') ? $this->maskCpf($this->user_cpf) : null,
            'patient_cpf' => $request->boolean('include_cpf') ? $this->maskCpf($this->patient_cpf) : null,
            'event_type' => $this->enumValue($this->event_type),
            'timestamp' => optional($this->occurred_at)->timezone(config('audit.timezone'))->toIso8601String(),
            'metadata' => $this->metadata,
        ];
    }

    private function maskCpf(?string $cpf): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $cpf);

        if (strlen($digits) !== 11) {
            return null;
        }

        return substr($digits, 0, 3) . '.***.***-' . substr($digits, -2);
    }

    private function enumValue(mixed $value): mixed
    {
        return $value instanceof BackedEnum ? $value->value : $value;
    }
}
