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

    public function test_can_create_strategic_review_with_custom_and_existing_audit_questions(): void
    {
        $template = \App\Domains\Development\Models\AuditTemplate::firstOrCreate(
            ['module_id' => $this->module->id, 'tenant_id' => $this->tenant->id],
            ['name' => 'Test Audit Template', 'description' => 'For testing']
        );

        $existingQ = \App\Domains\Development\Models\AuditQuestion::create([
            'tenant_id'         => $this->tenant->id,
            'audit_template_id' => $template->id,
            'question_text'     => 'Kanonische Audit-Frage zur Nachfolge?',
            'weight'            => 1.0,
            'order'             => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('actions.review.create'), [
            'title'                 => 'Q3-2026 Strategy Review',
            'period'                => 'Q3-2026',
            'review_date'           => now()->toDateString(),
            'summary'               => 'Testing review creation with audit questions.',
            'questions'             => [
                'Wie transparent ist die Mitarbeiterkommunikation?',
                'Sind Notfall-Vollmachten vorhanden?',
            ],
            'question_modules'      => [
                $this->module->id,
                null,
            ],
            'existing_question_ids' => [
                $existingQ->id,
            ],
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $createdReview = Review::where('title', 'Q3-2026 Strategy Review')->first();
        $this->assertNotNull($createdReview);
        $this->assertEquals(Review::STATUS_OPEN, $createdReview->status);

        $items = $createdReview->items;
        $this->assertCount(3, $items);

        // Verify first custom question
        $this->assertTrue($items->contains(function ($item) {
            return $item->topic === 'Wie transparent ist die Mitarbeiterkommunikation?'
                && $item->module_id === $this->module->id
                && $item->status === 'identified';
        }));

        // Verify second custom question (without module)
        $this->assertTrue($items->contains(function ($item) {
            return $item->topic === 'Sind Notfall-Vollmachten vorhanden?'
                && $item->module_id === null
                && $item->status === 'identified';
        }));

        // Verify existing question
        $this->assertTrue($items->contains(function ($item) {
            return $item->topic === 'Kanonische Audit-Frage zur Nachfolge?'
                && $item->module_id === $this->module->id
                && $item->status === 'identified';
        }));
    }

    public function test_closing_already_closed_review_gracefully_redirects_with_info_message(): void
    {
        $review = Review::create([
            'tenant_id'   => $this->tenant->id,
            'title'       => 'Q1 Governance Review',
            'period'      => 'Q1-2026',
            'review_date' => now()->toDateString(),
            'status'      => Review::STATUS_CLOSED,
            'summary'     => 'Already completed review.',
            'created_by'  => $this->user->id,
            'closed_at'   => now(),
            'closed_by'   => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('actions.review.close', $review->id));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('info');
    }
}
