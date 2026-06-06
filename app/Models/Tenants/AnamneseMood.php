<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseMood extends Model
{
    use HasFactory;
    protected $table = 'anamnese_mood';

    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
