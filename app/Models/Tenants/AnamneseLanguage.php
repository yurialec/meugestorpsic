<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnamneseLanguage extends Model
{
    use HasFactory;
    protected $table = 'anamnese_language';

    protected $guarded = [];

    public function anamnese()
    {
        return $this->belongsTo(Anamnese::class);
    }
}
