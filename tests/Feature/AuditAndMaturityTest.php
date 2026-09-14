<?php

namespace Tests\Feature;

use App\Domains\Development\Actions\RunAudit;
use App\Domains\Development\Actions\ScoreAudit;
use App\Domains\Development\Models\AuditQuestion;
use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Services\MaturityScoringService;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuditAndMaturityTest extends TestCase
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
            ['email' => 'auditor@disavo.test'],
            ['name' => 'Test Auditor', 'password' => bcrypt('secret')]
        );

        $this->module = Module::where('slug', 'markenaufbau')->firstOrFail();
    }

    public function test_audit_execution_and_weighted_maturity_scoring(): void
    {
        // 1. Goal (Zielzustand): Target maturity = 90%
        Goal::create([
            'tenant_id'    => $this->tenant->id,
            'module_id'    => $this->module->id,
            'title'        => 'Zielzustand Markenaufbau',
            'target_score' => 90.00,
            'version'      => 1,
            'created_by'   => $this->user->id,
        ]);

        // 2. Audit Template with DB-driven weights
        $template = AuditTemplate::create([
            'tenant_id' => $this->tenant->id,
            'module_id' => $this->module->id,
            'name'      => 'Brand Maturity Audit',
        ]);

        // Question A: Weight 30, MaxScore 5
        $qA = AuditQuestion::create([
            'audit_template_id' => $template->id,
            'question_text'     => 'Is the brand guidelines document implemented?',
            'weight'            => 30,
            'max_score'         => 5,
        ]);

        // Question B: Weight 10, MaxScore 5
        $qB = AuditQuestion::create([
            'audit_template_id' => $template->id,
            'question_text'     => 'Is social media monitored weekly?',
            'weight'            => 10,
            'max_score'         => 5,
        ]);

        // 3. Run audit
        $runAction = app(RunAudit::class);
        $run = $runAction->handle($this->module, $template, $this->user);

        $this->assertEquals(AuditRun::STATUS_IN_PROGRESS, $run->status);
        $this->assertCount(2, $run->responses);

        // 4. Score audit:
        // Question A: 5/5 (100% * 30 = 30)
        // Question B: 2.5/5 (50% * 10 = 5)
        // Total: 35 / 40 = 87.5%
        $scoreAction = app(ScoreAudit::class);
        $scoredRun = $scoreAction->handle($run, [
            $qA->id => ['score' => 5, 'notes' => 'Complete CI/CD guidelines present.'],
            $qB->id => ['score' => 2, 'notes' => 'Manual ad-hoc monitoring only.'],
        ]);

        $this->assertEquals(AuditRun::STATUS_COMPLETED, $scoredRun->status);
        $this->assertNotNull($scoredRun->overall_score);

        // Calculation: (5/5 * 30 + 2/5 * 10) / (30 + 10) * 100 = (30 + 4) / 40 * 100 = 85.0%
        $this->assertEquals(85.00, $scoredRun->overall_score);

        // 5. Assert Graph connection: AuditRun -(Measures)-> Module
        $measuresEdges = $scoredRun->outgoingEdges()
            ->where('relation', RelationType::Measures->value)
            ->get();
        $this->assertCount(1, $measuresEdges);
        $this->assertEquals($this->module->id, $measuresEdges->first()->target_id);

        // 6. Maturity scoring service & gap calculation
        $scoringService = app(MaturityScoringService::class);
        $maturity = $scoringService->getMaturityDelta($this->module);

        $this->assertEquals(85.00, $maturity['current_score']);
        $this->assertEquals(90.00, $maturity['target_score']);
        $this->assertEquals(5.00, $maturity['gap']); // 90 - 85 = 5.0% gap
        $this->assertEquals('gap_detected', $maturity['status']);
    }

    public function test_maturity_api_endpoint(): void
    {
        $response = $this->getJson("/api/v1/modules/{$this->module->id}/maturity");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'module_id',
                'module_name',
                'current_score',
                'target_score',
                'gap',
                'status',
            ]);
    }
}
