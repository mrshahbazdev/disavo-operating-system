<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Knowledge\Models\Insight;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Knowledge\States\Principle\Active;
use App\Domains\Review\Models\Review;
use App\Domains\Review\Models\ReviewItem;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class Phase3KnowledgeGraphTest extends TestCase
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
            ['email' => 'graph_steward@disavo.test'],
            ['name' => 'Graph Steward', 'password' => bcrypt('secret')]
        );

        $this->module = Module::firstOrCreate(
            ['slug' => 'graph-test-module'],
            ['name' => 'Graph Test Module', 'tenant_id' => $this->tenant->id]
        );
    }

    public function test_cytoscape_neighborhood_returns_multi_hop_elements_and_stats(): void
    {
        // Setup a 4-hop chain: Observation -> Insight -> Learning -> Principle -> Module
        $obs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Founder operational drag',
            'content'    => 'Founders spend 40% time on operational fires.',
            'context'    => ['source' => 'Q1 interview series'],
            'created_by' => $this->user->id,
        ]);

        $insight = Insight::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Operational gravity drags strategic focus',
            'summary'    => 'Without explicit systems, founders default to operational firefighting.',
            'created_by' => $this->user->id,
        ]);

        $learning = Learning::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Delegation architectures unlock executive capacity',
            'summary'    => 'Formalizing core processes reduces founder dependency by 60%.',
            'state'      => Draft::class,
            'created_by' => $this->user->id,
        ]);

        $principle = Principle::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Subsidiarity of Execution',
            'statement'  => 'No decision should be made at a higher level than necessary.',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $this->user->id,
        ]);

        // Link the chain
        $obs->linkTo($insight, RelationType::Produces);
        $insight->linkTo($learning, RelationType::Validates);
        $learning->linkTo($principle, RelationType::Promotes);
        $principle->linkTo($this->module, RelationType::Governs);

        $response = $this->actingAs($this->user)
            ->withHeader('X-Tenant', $this->tenant->slug)
            ->getJson("/api/v1/graph/observation/{$obs->id}?depth=4");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'elements' => [
                    'nodes' => [
                        '*' => [
                            'data' => [
                                'id',
                                'label',
                                'type',
                                'type_label',
                                'model_id',
                                'is_center',
                            ],
                        ],
                    ],
                    'edges' => [
                        '*' => [
                            'data' => [
                                'id',
                                'source',
                                'target',
                                'relation',
                            ],
                        ],
                    ],
                ],
                'stats' => [
                    'center',
                    'depth',
                    'node_count',
                    'edge_count',
                ],
            ]);

        $data = $response->json();
        $this->assertEquals("observation:{$obs->id}", $data['stats']['center']);
        $this->assertGreaterThanOrEqual(5, $data['stats']['node_count']);
        $this->assertGreaterThanOrEqual(4, $data['stats']['edge_count']);

        // Assert center node is flagged
        $centerNode = collect($data['elements']['nodes'])->firstWhere('data.id', "observation:{$obs->id}");
        $this->assertNotNull($centerNode);
        $this->assertTrue($centerNode['data']['is_center']);
    }

    public function test_contradiction_detection_surfaces_active_tensions_and_governed_modules(): void
    {
        $principle = Principle::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Centralized Allocation Principle',
            'statement'  => 'All capital allocation decisions require central investment committee approval.',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $this->user->id,
        ]);

        $principle->linkTo($this->module, RelationType::Governs);

        $learning = Learning::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Decentralized Micro-Budgets Outperform Committee Approval',
            'summary'    => 'Subsidiaries move 3x faster with autonomous budgets up to 50k EUR.',
            'state'      => Draft::class,
            'created_by' => $this->user->id,
        ]);

        // Learning explicitly contradicts the active Principle
        $learning->linkTo($principle, RelationType::Contradicts, [
            'hypothesis' => 'Centralization created a 6-week bottleneck in module procurement.',
        ]);

        $response = $this->actingAs($this->user)
            ->withHeader('X-Tenant', $this->tenant->slug)
            ->getJson('/api/v1/graph/contradictions');

        $response->assertStatus(200);
        $json = $response->json();

        $this->assertNotEmpty($json['data']);
        $tension = collect($json['data'])->firstWhere('learning.id', $learning->id);

        $this->assertNotNull($tension, 'Contradiction tension must be surfaced in graph/contradictions API.');
        $this->assertEquals('critical', $tension['tension_severity']);
        $this->assertEquals($principle->id, $tension['principle']['id']);
        $this->assertEquals($principle->title, $tension['principle']['title']);

        // Verify governed module is surfaced
        $governedModuleIds = collect($tension['governed_modules'])->pluck('id')->all();
        $this->assertContains($this->module->id, $governedModuleIds);
    }

    public function test_orphan_detection_identifies_isolated_nodes_and_resolves_when_connected(): void
    {
        $orphanObs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Isolated Observation',
            'content'    => 'Completely isolated observation with no edges whatsoever.',
            'context'    => ['source' => 'Orphan test'],
            'created_by' => $this->user->id,
        ]);

        $orphanTool = Tool::create([
            'tenant_id'    => $this->tenant->id,
            'module_id'    => $this->module->id,
            'name'         => 'Disconnected Standalone Tool',
            'type'         => Tool::TYPE_TEMPLATE,
            'description'  => 'No graph edges attached yet',
        ]);

        $response = $this->actingAs($this->user)
            ->withHeader('X-Tenant', $this->tenant->slug)
            ->getJson('/api/v1/graph/orphans');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertGreaterThanOrEqual(2, $data['total_orphans']);
        $orphanIds = collect($data['orphans'])->pluck('id')->all();
        $this->assertContains("observation:{$orphanObs->id}", $orphanIds);
        $this->assertContains("tool:{$orphanTool->id}", $orphanIds);

        // Also test artisan command dos:graph:orphans
        $exitCode = Artisan::call('dos:graph:orphans', [
            '--tenant' => $this->tenant->id,
        ]);
        $this->assertSame(0, $exitCode);

        // Now connect the observation to an insight
        $insight = Insight::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Resolved insight',
            'summary'    => 'Connected insight',
            'created_by' => $this->user->id,
        ]);
        $orphanObs->linkTo($insight, RelationType::Produces);

        // Re-check orphans
        $secondResponse = $this->actingAs($this->user)
            ->withHeader('X-Tenant', $this->tenant->slug)
            ->getJson('/api/v1/graph/orphans');

        $secondData = $secondResponse->json();
        $updatedOrphanIds = collect($secondData['orphans'])->pluck('id')->all();

        // Observation should no longer be an orphan
        $this->assertNotContains("observation:{$orphanObs->id}", $updatedOrphanIds);
    }

    public function test_backfill_command_converts_foreign_keys_into_graph_edges_non_destructively(): void
    {
        // 1. Unlinked KPI
        $kpi = Kpi::create([
            'tenant_id'    => $this->tenant->id,
            'module_id'    => $this->module->id,
            'name'         => 'Backfill Test KPI',
            'code'         => 'BT-KPI-1',
            'unit'         => '%',
            'direction'    => 'maximize',
            'target_value' => 85.0,
        ]);

        // 2. Unlinked Tool
        $tool = Tool::create([
            'tenant_id'    => $this->tenant->id,
            'module_id'    => $this->module->id,
            'name'         => 'Backfill Test Tool',
            'type'         => Tool::TYPE_CHECKLIST,
            'description'  => 'Pre-flight check list',
        ]);

        // 3. Unlinked Review with ReviewItem pointing to module
        $review = Review::create([
            'tenant_id'   => $this->tenant->id,
            'title'       => 'Backfill Quarterly Review',
            'period'      => 'Q3-2026',
            'review_date' => now()->toDateString(),
            'status'      => Review::STATUS_OPEN,
            'created_by'  => $this->user->id,
        ]);

        ReviewItem::create([
            'review_id' => $review->id,
            'module_id' => $this->module->id,
            'topic'     => 'Module governance audit',
            'status'    => 'pending',
        ]);

        // Assert edges do NOT exist yet
        $hasKpiEdgeBefore = KnowledgeEdge::withoutGlobalScopes()
            ->where('source_type', NodeType::Kpi->value)
            ->where('source_id', $kpi->id)
            ->exists();
        $this->assertFalse($hasKpiEdgeBefore);

        $hasToolEdgeBefore = KnowledgeEdge::withoutGlobalScopes()
            ->where('source_type', NodeType::Tool->value)
            ->where('source_id', $tool->id)
            ->exists();
        $this->assertFalse($hasToolEdgeBefore);

        $hasReviewEdgeBefore = KnowledgeEdge::withoutGlobalScopes()
            ->where('source_type', NodeType::Review->value)
            ->where('source_id', $review->id)
            ->exists();
        $this->assertFalse($hasReviewEdgeBefore);

        // Run backfill command
        $exitCode = Artisan::call('dos:graph:backfill', [
            '--tenant' => $this->tenant->id,
        ]);
        $this->assertSame(0, $exitCode);

        // Assert edges now exist
        $kpiEdge = KnowledgeEdge::withoutGlobalScopes()
            ->active()
            ->where('source_type', NodeType::Kpi->value)
            ->where('source_id', $kpi->id)
            ->where('target_type', NodeType::Module->value)
            ->where('target_id', $this->module->id)
            ->where('relation', RelationType::Quantifies->value)
            ->first();
        $this->assertNotNull($kpiEdge, 'KPI -> Module (quantifies) edge must be created by backfill');

        $toolEdge = KnowledgeEdge::withoutGlobalScopes()
            ->active()
            ->where('source_type', NodeType::Tool->value)
            ->where('source_id', $tool->id)
            ->where('target_type', NodeType::Module->value)
            ->where('target_id', $this->module->id)
            ->where('relation', RelationType::Improves->value)
            ->first();
        $this->assertNotNull($toolEdge, 'Tool -> Module (improves) edge must be created by backfill');

        $reviewEdge = KnowledgeEdge::withoutGlobalScopes()
            ->active()
            ->where('source_type', NodeType::Review->value)
            ->where('source_id', $review->id)
            ->where('target_type', NodeType::Module->value)
            ->where('target_id', $this->module->id)
            ->where('relation', RelationType::Evaluates->value)
            ->first();
        $this->assertNotNull($reviewEdge, 'Review -> Module (evaluates) edge must be created by backfill');

        // Run backfill again to test idempotency
        $edgeCountBeforeSecondRun = KnowledgeEdge::withoutGlobalScopes()->count();
        Artisan::call('dos:graph:backfill', ['--tenant' => $this->tenant->id]);
        $edgeCountAfterSecondRun = KnowledgeEdge::withoutGlobalScopes()->count();

        $this->assertSame(
            $edgeCountBeforeSecondRun,
            $edgeCountAfterSecondRun,
            'Running backfill multiple times must be idempotent and not create duplicate edges'
        );
    }
}
