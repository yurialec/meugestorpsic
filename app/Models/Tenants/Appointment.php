<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;

class Appointment extends BaseTenantModel
{
    protected $table = 'client_appointments';

    const STATUS_OPEN = 'Open';
    const STATUS_CONFIRMED = 'Confirmed';
    const STATUS_DONE = 'Done';
    const STATUS_CANCELED = 'Canceled';

    protected $fillable = [
        'employee_id',
        'patient_id',
        'tenant_id',
        'day',
        'hour',
        'status',
    ];
}
