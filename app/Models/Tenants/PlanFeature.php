<?php

namespace App\Models\Tenants;

use App\Models\Admin\Plan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    use HasFactory;

    protected $table = 'plan_features';

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
