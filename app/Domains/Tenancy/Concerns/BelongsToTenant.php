<?php

declare(strict_types=1);

namespace App\Domains\Tenancy\Concerns;

use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model): void {
            if (empty($model->tenant_id) && TenantContext::check()) {
                $model->tenant_id = TenantContext::getTenantId();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
