<?php

namespace Tests\Feature;

use App\Domains\Graph\Actions\CreateEdge;
use App\Domains\Graph\Actions\InvalidateEdge;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Graph\Queries\ImpactRadiusQuery;
use App\Domains\Graph\Queries\ProvenancePathQuery;
use App\Domains\Knowledge\Models\Decision;
use App\Domains\Knowledge\Models\Insight;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Knowledge\States\Principle\Active;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use LogicException;
use Tests\TestCase;

class KnowledgeGraphTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Disavo Test Tenant',
            'slug' => 'disavo-test',
        ]);

        TenantContext::setTenant($this->tenant);

        $this->user = User::create([
            'name'     => 'Test Steward',
            'email'    => 'steward@test.de',
            'password' => bcrypt('secret'),
        ]);
    }

    public function test_can_link_nodes_and_retrieve_related(): void
    {
        $obs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Customer complaints spike',
            'content'    => 'Feedback on rapid feature rollout',
            'created_by' => $this->user->id,
        ]);

        $insight = Insight::create([
            'tenant_id'    => $this->tenant->id,
            'title'        => 'Rollouts need staged beta testing',
            'summary'      => 'Quality assurance gap in rapid delivery',
            'created_by'   => $this->user->id,
        ]);

        $edge = $obs->linkTo($insight, RelationType::Produces, ['note' => 'empirical']);

        $this->assertInstanceOf(KnowledgeEdge::class, $edge);
        $this->assertTrue($edge->isActive());
        $this->assertEquals(RelationType::Produces, $edge->relation);

        $related = $obs->related(RelationType::Produces);
        $this->assertCount(1, $related);
        $this->assertEquals($insight->id, $related->first()->id);
    }

    public function test_cannot_link_cross_tenant_nodes(): void
    {
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other-tenant',
        ]);

        $obs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Observation A',
            'content'    => 'Detail',
            'created_by' => $this->user->id,
        ]);

        $otherInsight = Insight::create([
            'tenant_id'  => $otherTenant->id,
            'title'      => 'Insight B',
            'summary'    => 'Cross detail',
            'created_by' => $this->user->id,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $obs->linkTo($otherInsight, RelationType::Produces);
    }

    public function test_cannot_create_illegal_relation(): void
    {
        $obs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Direct Observation',
            'content'    => 'Obs',
            'created_by' => $this->user->id,
        ]);

        $principle = Principle::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Direct Principle',
            'statement'  => 'Illegal direct link',
            'state'      => Active::class,
            'created_by' => $this->user->id,
        ]);

        // Observation cannot directly Promotes to Principle (Promotes is strictly Learning -> Principle)
        $this->expectException(InvalidArgumentException::class);
        $obs->linkTo($principle, RelationType::Promotes);
    }

    public function test_edges_are_append_only_and_cannot_be_deleted(): void
    {
        $obs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Obs',
            'content'    => 'Content',
            'created_by' => $this->user->id,
        ]);

        $insight = Insight::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Insight',
            'summary'    => 'Summary',
            'created_by' => $this->user->id,
        ]);

        $edge = $obs->linkTo($insight, RelationType::Produces);

        // Deleting edge must throw exception
        $this->expectException(LogicException::class);
        $edge->delete();
    }

    public function test_edges_can_be_invalidated_with_documented_reason(): void
    {
        $obs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Obs',
            'content'    => 'Content',
            'created_by' => $this->user->id,
        ]);

        $insight = Insight::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Insight',
            'summary'    => 'Summary',
            'created_by' => $this->user->id,
        ]);

        $edge = $obs->linkTo($insight, RelationType::Produces);

        app(InvalidateEdge::class)->handle($edge, $this->user, 'Falsified by new empirical market test');

        $this->assertFalse($edge->fresh()->isActive());
        $this->assertNotNull($edge->fresh()->invalidated_at);
        $this->assertEquals('Falsified by new empirical market test', $edge->fresh()->invalidation_reason);

        // Inactive edges are filtered out of active relations
        $this->assertCount(0, $obs->related(RelationType::Produces));
    }

    public function test_recursive_provenance_path_trace(): void
    {
        // Construct complete causal chain:
        // Obs -> Insight -> Learning -> Principle -> Decision
        $obs = Observation::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Empirical finding on communication delay',
            'content'    => 'Delay hurts trust',
            'created_by' => $this->user->id,
        ]);

        $insight = Insight::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Communication requires 12 months minimum',
            'summary'    => 'Trust decay metric',
            'created_by' => $this->user->id,
        ]);
        $obs->linkTo($insight, RelationType::Produces);

        $learning = Learning::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Succession framework rule',
            'summary'    => 'Prepare 12 months ahead',
            'state'      => Draft::class,
            'created_by' => $this->user->id,
        ]);
        $insight->linkTo($learning, RelationType::Validates);

        $principle = Principle::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Succession Principle',
            'statement'  => 'Begin communication 12 months prior',
            'state'      => Active::class,
            'created_by' => $this->user->id,
        ]);
        $learning->linkTo($principle, RelationType::Promotes);

        $decision = Decision::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Mandatory communication checklist adoption',
            'summary'    => 'Adopted for all holdings',
            'created_by' => $this->user->id,
        ]);
        $decision->linkTo($principle, RelationType::JustifiedBy);

        $query = new ProvenancePathQuery();
        $trail = $query->trace(NodeType::Decision, (int) $decision->id, (int) $this->tenant->id);

        $this->assertCount(4, $trail);
        // Step 1 depth: Decision -> Principle
        $this->assertEquals('decision', $trail[0]->source_type);
        $this->assertEquals('principle', $trail[0]->target_type);
        $this->assertEquals('justified_by', $trail[0]->relation);

        // Forward impact query
        $impactQuery = new ImpactRadiusQuery();
        $downstream = $impactQuery->trace(NodeType::Observation, (int) $obs->id, (int) $this->tenant->id);
        $this->assertCount(3, $downstream);
    }

    public function test_cycle_guard_prevents_infinite_recursion(): void
    {
        // Learning -> Question -> Learning (cyclic raises/answers)
        $learning = Learning::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Cycle Learning',
            'summary'    => 'Cycle test',
            'state'      => Draft::class,
            'created_by' => $this->user->id,
        ]);

        $question = Question::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Cycle Question',
            'context'    => 'Cycle context',
            'created_by' => $this->user->id,
        ]);

        // Learning raises Question
        $learning->linkTo($question, RelationType::Raises);
        // Question answered by Learning (creates cyclic loop)
        $learning->linkTo($question, RelationType::Answers);

        $query = new ProvenancePathQuery();
        // Trace on question must terminate cleanly without crashing or infinite looping
        $trail = $query->trace(NodeType::Question, (int) $question->id, (int) $this->tenant->id, 10);

        $this->assertIsArray($trail);
        $this->assertLessThanOrEqual(5, count($trail));
    }
}
