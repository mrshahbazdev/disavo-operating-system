<?php

namespace Tests\Feature;

use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Actions\PromoteLearningToPrinciple;
use App\Domains\Knowledge\Actions\SupersedePrinciple;
use App\Domains\Knowledge\Actions\ValidateLearning;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\States\Learning\Deprecated;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Knowledge\States\Learning\Promoted;
use App\Domains\Knowledge\States\Learning\Rejected;
use App\Domains\Knowledge\States\Learning\UnderValidation;
use App\Domains\Knowledge\States\Learning\Validated;
use App\Domains\Knowledge\States\Principle\Active;
use App\Domains\Knowledge\States\Principle\Proposed;
use App\Domains\Knowledge\States\Principle\Retired;
use App\Domains\Knowledge\States\Principle\Superseded;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Spatie\ModelStates\Exceptions\TransitionNotFound;
use Tests\TestCase;

class KnowledgeStateTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'State Test Tenant',
            'slug' => 'state-tenant',
        ]);
        TenantContext::setTenant($this->tenant);

        $this->user = User::create([
            'name'     => 'Steward User',
            'email'    => 'steward_state@test.de',
            'password' => bcrypt('secret'),
        ]);
    }

    public function test_learning_lifecycle_transitions(): void
    {
        $learning = Learning::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Lifecycle test',
            'summary'    => 'Summary text',
            'state'      => Draft::class,
            'created_by' => $this->user->id,
        ]);

        $this->assertInstanceOf(Draft::class, $learning->state);

        // Transition: Draft -> UnderValidation
        $learning->state->transitionTo(UnderValidation::class);
        $this->assertInstanceOf(UnderValidation::class, $learning->state);

        // Transition: UnderValidation -> Validated
        app(ValidateLearning::class)->handle($learning, $this->user);
        $this->assertInstanceOf(Validated::class, $learning->state);
        $this->assertNotNull($learning->validated_at);
        $this->assertEquals($this->user->id, $learning->validated_by);

        // Promotion: Validated -> Promoted creates Principle & Promotes edge
        $principle = app(PromoteLearningToPrinciple::class)->handle(
            learning: $learning,
            steward: $this->user,
            statement: 'Derived core rule'
        );

        $this->assertInstanceOf(Promoted::class, $learning->fresh()->state);
        $this->assertInstanceOf(Principle::class, $principle);
        $this->assertInstanceOf(Active::class, $principle->state);

        // Assert Promotes edge exists
        $promotesEdges = $learning->outgoingEdges()
            ->where('relation', RelationType::Promotes->value)
            ->get();
        $this->assertCount(1, $promotesEdges);
        $this->assertEquals($principle->id, $promotesEdges->first()->target_id);
    }

    public function test_cannot_promote_unvalidated_learning(): void
    {
        $learning = Learning::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Unvalidated Draft',
            'summary'    => 'Draft summary',
            'state'      => Draft::class,
            'created_by' => $this->user->id,
        ]);

        $this->expectException(InvalidArgumentException::class);
        app(PromoteLearningToPrinciple::class)->handle(
            learning: $learning,
            steward: $this->user,
            statement: 'Premature rule'
        );
    }

    public function test_principle_supersede_preserves_version_history(): void
    {
        $v1 = Principle::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Customer SLA rule',
            'statement'  => 'Respond to tickets within 24 hours',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $this->user->id,
        ]);

        $v2 = app(SupersedePrinciple::class)->handle(
            oldPrinciple: $v1,
            actor: $this->user,
            newStatement: 'Respond to critical tickets within 4 hours and standard within 24 hours',
            changeReason: 'Escalation bottleneck identified in Q1 review'
        );

        $this->assertInstanceOf(Superseded::class, $v1->fresh()->state);
        $this->assertEquals($v2->id, $v1->fresh()->superseded_by_id);

        $this->assertInstanceOf(Active::class, $v2->state);
        $this->assertEquals(2, $v2->version);

        // Check Supersedes edge: v2 -(Supersedes)-> v1
        $supersedesEdges = $v2->outgoingEdges()
            ->where('relation', RelationType::Supersedes->value)
            ->get();
        $this->assertCount(1, $supersedesEdges);
        $this->assertEquals($v1->id, $supersedesEdges->first()->target_id);
        $this->assertEquals('Escalation bottleneck identified in Q1 review', $supersedesEdges->first()->meta['reason']);
    }

    public function test_principle_retirement(): void
    {
        $principle = Principle::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Legacy Tool Rule',
            'statement'  => 'Use SVN for all version control',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $this->user->id,
        ]);

        $principle->state->transitionTo(Retired::class);
        $this->assertInstanceOf(Retired::class, $principle->fresh()->state);
    }
}
