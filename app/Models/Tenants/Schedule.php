<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'client_schedules';

    protected $fillable = [
        'tenant_id',
        'client_id',
        'day_of_week',
        'start_time',
        'end_time',
        'start_break_time',
        'end_break_time',
    ];
}