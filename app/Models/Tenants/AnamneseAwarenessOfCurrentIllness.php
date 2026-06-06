<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseAwarenessOfCurrentIllness extends Model
{

    use HasFactory;
    protected $table = 'anamnese_awareness_of_current_illness';
    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
