<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmInteraction extends Model
{
    protected $fillable = [
        'client_id',
        'status_id',
        'observation',
        'attachment',
        'user_id',
        'alarm',
    ];

    public function status()
    {
        return $this->belongsTo(CrmStatus::class);
    }

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }
}