<?php

namespace Tests\Feature;

use App\Domains\Development\Models\Module;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModuleHierarchyTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'disavo'],
            ['name' => 'Disavo Holding GmbH']
        );
        TenantContext::setTenant($this->tenant);
    }

    public function test_the_six_canonical_modules_exist(): void
    {
        $expectedSlugs = [
            'unternehmensentwicklung',
            'markenaufbau',
            'nachfolge',
            'unternehmerentwicklung',
            'beteiligungsmanagement',
            'kapitalallokation',
        ];

        foreach ($expectedSlugs as $slug) {
            $this->assertDatabaseHas('modules', [
                'tenant_id' => $this->tenant->id,
                'slug'      => $slug,
            ]);
        }
    }

    public function test_can_build_nested_module_subtrees(): void
    {
        $root = Module::where('slug', 'unternehmensentwicklung')->firstOrFail();

        $child1 = Module::create([
            'tenant_id' => $this->tenant->id,
            'parent_id' => $root->id,
            'name'      => 'Prozessoptimierung',
            'slug'      => 'prozessoptimierung',
            'order'     => 1,
        ]);

        $child2 = Module::create([
            'tenant_id' => $this->tenant->id,
            'parent_id' => $child1->id,
            'name'      => 'ERP Einführung',
            'slug'      => 'erp-einfuehrung',
            'order'     => 1,
        ]);

        // Adjacency list recursive traversal
        $descendants = $root->descendants()->get();
        $this->assertCount(2, $descendants);
        $this->assertTrue($descendants->contains('id', $child1->id));
        $this->assertTrue($descendants->contains('id', $child2->id));

        $ancestors = $child2->ancestors()->get();
        $this->assertCount(2, $ancestors);
        $this->assertTrue($ancestors->contains('id', $root->id));
    }

    public function test_principle_governs_module_via_graph_edge(): void
    {
        $nachfolge = Module::where('slug', 'nachfolge')->firstOrFail();
        $principle = Principle::where('title', 'Grundsatz Nachfolgekommunikation')->firstOrFail();

        // Edge: Principle GOVERNS Module
        $edges = $principle->outgoingEdges()
            ->where('relation', RelationType::Governs->value)
            ->get();

        $this->assertCount(1, $edges);
        $this->assertEquals($nachfolge->id, $edges->first()->target_id);

        $governedModules = $principle->related(RelationType::Governs);
        $this->assertCount(1, $governedModules);
        $this->assertEquals($nachfolge->id, $governedModules->first()->id);
    }
}
