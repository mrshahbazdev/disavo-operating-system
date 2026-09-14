<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->header('X-Tenant-ID')
            ?? $request->input('tenant_id')
            ?? $request->user()?->tenants()->first()?->id;

        if ($tenantId !== null) {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                TenantContext::setTenant($tenant);

                // Configure Spatie Permission tenant/team scope
                if (function_exists('setPermissionsTeamId')) {
                    setPermissionsTeamId($tenant->id);
                }
            }
        }

        return $next($request);
    }
}
