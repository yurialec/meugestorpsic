<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseGuidance extends Model
{
    use HasFactory;
    protected $table = 'anamnese_guidance';
    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
