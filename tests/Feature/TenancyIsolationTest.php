<?php

namespace Tests\Feature;

use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\States\Principle\Active;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TenancyIsolationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_models_are_isolated_by_tenant_scope(): void
    {
        $tenantA = Tenant::create(['name' => 'Company A', 'slug' => 'company-a']);
        $tenantB = Tenant::create(['name' => 'Company B', 'slug' => 'company-b']);

        $user = User::create([
            'name'     => 'Alice',
            'email'    => 'alice@test.de',
            'password' => bcrypt('password'),
        ]);

        // In Tenant A context
        TenantContext::setTenant($tenantA);
        $obsA = Observation::create([
            'title'      => 'Observation for Tenant A',
            'content'    => 'Internal confidential findings for Company A',
            'created_by' => $user->id,
        ]);
        $this->assertEquals($tenantA->id, $obsA->tenant_id);

        // In Tenant B context
        TenantContext::setTenant($tenantB);
        $obsB = Observation::create([
            'title'      => 'Observation for Tenant B',
            'content'    => 'Findings for Company B',
            'created_by' => $user->id,
        ]);
        $this->assertEquals($tenantB->id, $obsB->tenant_id);

        // Querying while in Tenant B context must ONLY return Tenant B records
        $resultsTenantB = Observation::all();
        $this->assertCount(1, $resultsTenantB);
        $this->assertEquals('Observation for Tenant B', $resultsTenantB->first()->title);

        // Switch back to Tenant A context
        TenantContext::setTenant($tenantA);
        $resultsTenantA = Observation::all();
        $this->assertCount(1, $resultsTenantA);
        $this->assertEquals('Observation for Tenant A', $resultsTenantA->first()->title);

        // Clear context: without tenant context, all can be seen with explicit admin queries
        TenantContext::clear();
        $all = Observation::all();
        $this->assertTrue($all->contains('id', $obsA->id));
        $this->assertTrue($all->contains('id', $obsB->id));
    }
}
