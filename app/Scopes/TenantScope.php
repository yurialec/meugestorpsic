<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $tenantId = session('tenant_id');

        if ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        }
    }
}