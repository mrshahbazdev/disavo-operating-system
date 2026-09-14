<?php

declare(strict_types=1);

namespace App\Domains\Tenancy\Context;

use App\Domains\Tenancy\Models\Tenant;

class TenantContext
{
    protected static ?Tenant $currentTenant = null;

    public static function setTenant(?Tenant $tenant): void
    {
        static::$currentTenant = $tenant;
    }

    public static function getTenant(): ?Tenant
    {
        return static::$currentTenant;
    }

    public static function getTenantId(): ?int
    {
        return static::$currentTenant?->id;
    }

    public static function check(): bool
    {
        return static::$currentTenant !== null;
    }

    public static function clear(): void
    {
        static::$currentTenant = null;
    }
}
