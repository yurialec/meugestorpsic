<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseTreatment extends Model
{
    use HasFactory;
    protected $table = 'anamnese_treatment';
    protected $guarded = [];
}
