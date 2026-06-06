<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseDiagnosticHypothesis extends Model
{
    use HasFactory;
    protected $table = 'anamnese_diagnostic_hypothesis';
    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
