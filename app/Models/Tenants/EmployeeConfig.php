<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeConfig extends Model
{
    use HasFactory;

    protected $table = 'employee_config';

    protected $fillable = [
        'employee_id',
        'consultation_price',
        'consultation_duration',
    ];
}
