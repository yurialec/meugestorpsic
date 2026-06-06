<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseHistoryOfPresentIllness extends Model
{
    use HasFactory;
    protected $table = 'anamnese_history_of_present_illness';

    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
