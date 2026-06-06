<?php

namespace App\Models\Tenants;

use App\Casts\EncryptedSessionCast;
use App\Models\Admin\Clients;

class Consultation extends BaseTenantModel
{
    protected $table = "consultations";

    private const SENSITIVE_ENCRYPTED_FIELDS = [
        'objectives',
        'content_worked',
        'clinical_observations',
        'interventions',
        'planning',
        'homework',
        'emotional_state',
        'engagement_level',
        'insights',
    ];

    protected $fillable = [
        'tenant_id',
        'patient_id',
        'client_appointment_id',
        'scheduled_at',
        'started_at',
        'ended_at',
        'objectives',
        'content_worked',
        'clinical_observations',
        'interventions',
        'planning',
        'homework',
        'emotional_state',
        'engagement_level',
        'insights',
        'status',
        'location',
        'employee_id',
    ];

    protected $casts = [
        'objectives' => EncryptedSessionCast::class,
        'content_worked' => EncryptedSessionCast::class,
        'clinical_observations' => EncryptedSessionCast::class,
        'interventions' => EncryptedSessionCast::class,
        'planning' => EncryptedSessionCast::class,
        'homework' => EncryptedSessionCast::class,
        'emotional_state' => EncryptedSessionCast::class,
        'engagement_level' => EncryptedSessionCast::class,
        'insights' => EncryptedSessionCast::class,
    ];

    /**
     * @return array<int, string>
     */
    public static function sensitiveEncryptedFields(): array
    {
        return self::SENSITIVE_ENCRYPTED_FIELDS;
    }

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
