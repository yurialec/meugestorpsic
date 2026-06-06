<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseAttitudeWithTheInterviewer extends Model
{
    use HasFactory;

    protected $table = 'anamnese_attitude_with_the_interviewer';
    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
