<?php

namespace App\Http\Traits;

use App\Contracts\Audit\AuditLogger;
use App\Enums\AuditEventType;
use App\Models\Tenants\Patient;
use Illuminate\Http\Request;

trait LogsRecordDownloads
{
    protected function auditRecordDownload(Patient $patient, Request $request, array $metadata = []): void
    {
        app(AuditLogger::class)->log(AuditEventType::RecordDownload, [
            'patient_cpf' => $patient->cpf,
            'metadata' => [
                'patient_id' => $patient->id,
                'record_version' => $metadata['record_version'] ?? null,
                'motivo' => $metadata['motivo'] ?? $request->input('motivo'),
                ...$metadata,
            ],
        ], $request);
    }

    protected function auditRecordView(Patient $patient, Request $request, array $metadata = []): void
    {
        app(AuditLogger::class)->log(AuditEventType::RecordView, [
            'patient_cpf' => $patient->cpf,
            'metadata' => [
                'patient_id' => $patient->id,
                'record_version' => $metadata['record_version'] ?? null,
                'motivo' => $metadata['motivo'] ?? $request->input('motivo'),
                ...$metadata,
            ],
        ], $request);
    }
}
