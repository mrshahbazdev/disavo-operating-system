<?php

namespace Tests\Feature;

use App\Domains\Development\Models\Module;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
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

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
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

    public function test_authenticated_user_can_create_new_module_with_zielzustand(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'module_creator@disavo.de'],
            ['name' => 'Module Architect', 'password' => Hash::make('secret123')]
        );

        $response = $this->actingAs($user)->post('/actions/module/create', [
            'name'         => 'Digitale Transformation & IT',
            'description'  => 'Strategische Steuerung digitaler Werkzeuge und KI-Architekturen.',
            'target_title' => 'Skalierbare Cloud- & KI-Infrastruktur',
            'target_score' => 88.5,
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('modules', [
            'name' => 'Digitale Transformation & IT',
            'slug' => 'digitale-transformation-it',
        ]);

        $this->assertDatabaseHas('goals', [
            'title'        => 'Skalierbare Cloud- & KI-Infrastruktur',
            'target_score' => 88.5,
        ]);
    }

    public function test_can_create_module_with_amf_1_1_specification(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'amf_lead@disavo.de'],
            ['name' => 'AMF Framework Lead', 'password' => Hash::make('secret123')]
        );

        $response = $this->actingAs($user)->post('/actions/module/create', [
            'name'               => 'Vertriebsarchitektur & Skalierung',
            'development_object' => 'Vertrieb',
            'allocore_level'     => 'Ebene 1: Umsatz',
            'target_title'       => 'Abschlussstarke Vertriebspipeline mit 35% Conversion-Rate',
            'target_score'       => 92.0,
            'amf_version'        => 'v0.1 Konzept',
            'description'        => 'Systematische Lead-Qualifizierung und Skalierung der Abschlussquoten.',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('modules', [
            'name' => 'Vertriebsarchitektur & Skalierung',
            'slug' => 'vertriebsarchitektur-skalierung',
        ]);

        $module = Module::where('slug', 'vertriebsarchitektur-skalierung')->firstOrFail();
        $this->assertStringContainsString('Objekt: Vertrieb', $module->description);
        $this->assertStringContainsString('Ebene: Ebene 1: Umsatz', $module->description);

        $this->assertDatabaseHas('goals', [
            'module_id'    => $module->id,
            'title'        => 'Abschlussstarke Vertriebspipeline mit 35% Conversion-Rate',
            'target_score' => 92.0,
            'version'      => 1,
        ]);
    }
}
