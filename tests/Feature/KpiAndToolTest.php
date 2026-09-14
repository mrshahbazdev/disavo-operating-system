<?php

namespace Tests\Feature;

use App\Domains\Development\Actions\RecordKpiReading;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KpiAndToolTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $user;
    protected Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'disavo'],
            ['name' => 'Disavo Holding GmbH']
        );
        TenantContext::setTenant($this->tenant);

        $this->user = User::firstOrCreate(
            ['email' => 'kpi_user@disavo.test'],
            ['name' => 'KPI User', 'password' => bcrypt('secret')]
        );

        $this->module = Module::where('slug', 'beteiligungsmanagement')->firstOrFail();
    }

    public function test_kpi_time_series_recordings(): void
    {
        $kpi = Kpi::create([
            'tenant_id'    => $this->tenant->id,
            'module_id'    => $this->module->id,
            'name'         => 'EBITDA Marge Portfolio',
            'code'         => 'EBITDA_MARGIN',
            'unit'         => '%',
            'direction'    => 'higher_is_better',
            'target_value' => 20.0,
        ]);

        $action = app(RecordKpiReading::class);

        // Record month 1
        $r1 = $action->handle(
            kpi: $kpi,
            value: 17.5,
            recorder: $this->user,
            notes: 'January reading',
            recordedAt: now()->subMonth()
        );

        // Record month 2
        $r2 = $action->handle(
            kpi: $kpi,
            value: 19.2,
            recorder: $this->user,
            notes: 'February reading',
            recordedAt: now()
        );

        $readings = $kpi->readings()->get();
        $this->assertCount(2, $readings);
        $this->assertEquals(19.2, $kpi->latestReading()?->value);
    }

    public function test_tool_improves_module_via_graph(): void
    {
        $tool = Tool::create([
            'tenant_id'   => $this->tenant->id,
            'module_id'   => $this->module->id,
            'name'        => 'Portfolio Health Dashboard',
            'type'        => Tool::TYPE_SAAS,
            'url_or_path' => 'https://analytics.disavo.internal',
            'description' => 'Real-time telemetry for operational portfolio KPIs',
        ]);

        $edge = $tool->linkTo($this->module, RelationType::Improves, ['efficiency_gain' => '30%']);

        $this->assertNotNull($edge);
        $this->assertEquals(RelationType::Improves, $edge->relation);

        $improvedModules = $tool->related(RelationType::Improves);
        $this->assertCount(1, $improvedModules);
        $this->assertEquals($this->module->id, $improvedModules->first()->id);
    }
}
