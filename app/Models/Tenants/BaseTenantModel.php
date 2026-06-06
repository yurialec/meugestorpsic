<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;

abstract class BaseTenantModel extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {
            $query->where('tenant_id', session('tenant_id'));
        });

        static::creating(function ($model) {
            if (session('tenant_id')) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }
}