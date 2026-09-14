<?php

declare(strict_types=1);

namespace App\Domains\Tenancy\Scopes;

use App\Domains\Tenancy\Context\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (TenantContext::check()) {
            $builder->where($model->qualifyColumn('tenant_id'), TenantContext::getTenantId());
        }
    }
}
