<?php

namespace Tests\Feature;

use App\Domains\Development\Models\Module;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Actions\PromoteLearningToPrinciple;
use App\Domains\Knowledge\Actions\ValidateLearning;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Knowledge\States\Learning\Promoted;
use App\Domains\Knowledge\States\Principle\Active;
use App\Domains\Review\Actions\CloseReview;
use App\Domains\Review\Actions\SpawnLearningFromReview;
use App\Domains\Review\Models\Improvement;
use App\Domains\Review\Models\Review;
use App\Domains\Review\Models\ReviewItem;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewLoopTest extends TestCase
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
            ['email' => 'steward_review@disavo.test'],
            ['name' => 'Review Steward', 'password' => bcrypt('secret')]
        );

        $this->module = Module::where('slug', 'unternehmerentwicklung')->firstOrFail();
    }

    public function test_closing_review_spawns_draft_learning_and_closes_loop(): void
    {
        $review = Review::create([
            'tenant_id'   => $this->tenant->id,
            'title'       => 'H1 Strategy Review',
            'period'      => 'H1-2026',
            'review_date' => now()->toDateString(),
            'status'      => Review::STATUS_OPEN,
            'summary'     => 'Strategic review of founder bandwidth and delegation bottlenecks.',
            'created_by'  => $this->user->id,
        ]);

        $item = ReviewItem::create([
            'review_id' => $review->id,
            'module_id' => $this->module->id,
            'topic'     => 'Founder micromanagement in sales operations',
            'status'    => 'identified',
        ]);

        $improvement = Improvement::create([
            'review_id'      => $review->id,
            'review_item_id' => $item->id,
            'title'          => 'Delegate deal approvals up to 100k to sales lead',
            'action_plan'    => 'Establish formal delegation framework with weekly dashboard review.',
            'owner_id'       => $this->user->id,
            'status'         => Improvement::STATUS_IN_PROGRESS,
        ]);

        // Action: CloseReview with learning payload
        $closeAction = app(CloseReview::class);
        $closedReview = $closeAction->handle(
            review: $review,
            closer: $this->user,
            learningPayload: [
                'title'     => 'Delegation threshold increases deal velocity without increasing risk',
                'summary'   => 'Decentralized approval thresholds up to 100k reduced sales cycle by 40%.',
                'rationale' => 'Observed across 3 portfolio companies in H1 review.',
            ]
        );

        $this->assertEquals(Review::STATUS_CLOSED, $closedReview->status);
        $this->assertNotNull($closedReview->closed_at);

        // Assert: A new Learning was spawned in Draft state!
        $spawnedLearning = Learning::where('title', 'Delegation threshold increases deal velocity without increasing risk')->first();
        $this->assertNotNull($spawnedLearning);
        $this->assertInstanceOf(Draft::class, $spawnedLearning->state);

        // Assert: Graph Edge Review -(Generates)-> Learning exists!
        $generatesEdges = $review->outgoingEdges()
            ->where('relation', RelationType::Generates->value)
            ->get();
        $this->assertCount(1, $generatesEdges);
        $this->assertEquals($spawnedLearning->id, $generatesEdges->first()->target_id);

        // COMPLETE THE FULL LOOP:
        // 1. Steward validates the spawned Learning
        app(ValidateLearning::class)->handle($spawnedLearning, $this->user);

        // 2. Steward promotes Learning into a Principle
        $principle = app(PromoteLearningToPrinciple::class)->handle(
            learning: $spawnedLearning,
            steward: $this->user,
            statement: 'Operative Freigabegrenzen bis 100k liegen dezentral bei der Bereichsleitung.',
            principleTitle: 'Grundsatz Dezentrale Delegation'
        );

        $this->assertInstanceOf(Promoted::class, $spawnedLearning->fresh()->state);
        $this->assertInstanceOf(Active::class, $principle->state);

        // 3. Principle GOVERNS the module
        $principle->linkTo($this->module, RelationType::Governs);

        $governedModules = $principle->related(RelationType::Governs);
        $this->assertCount(1, $governedModules);
        $this->assertEquals($this->module->id, $governedModules->first()->id);
    }
}
