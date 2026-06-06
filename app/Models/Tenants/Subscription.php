<?php

namespace App\Models\Tenants;

use App\Models\Admin\Plan;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['name'];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}