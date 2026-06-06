<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Patient extends BaseTenantModel
{
    use HasUuids;

    protected $fillable = [
        'group',
        'gender',
        'full_name',
        'cpf',
        'email',
        'phone',
        'tenant_id',
        'date_of_birth',
        'employee_id'
    ];

    public function anamnese()
    {
        return $this->hasOne(
            Anamnese::class,
            'patient_id'
        );
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}