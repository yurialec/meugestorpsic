<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamnesePsychicExam extends Model
{
    use HasFactory;
    protected $table = 'anamnese_psychic_exam';

    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
