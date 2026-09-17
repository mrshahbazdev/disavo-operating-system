<?php

namespace Tests\Feature;

use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuditAndToolStudioTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $user;
    protected Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Disavo Test Holding',
            'slug' => 'disavo-test',
        ]);
        TenantContext::setTenant($this->tenant);

        $this->user = User::factory()->create();
        $this->user->tenants()->attach($this->tenant->id, ['role' => 'steward']);

        $this->module = Module::create([
            'name' => 'Markenaufbau',
            'slug' => 'markenaufbau',
            'description' => 'Brand Building and Authority',
        ]);
    }

    public function test_user_can_create_custom_audit_template_with_questions(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('actions.audit-template.create'), [
                'module_id' => $this->module->id,
                'name' => 'Spezial-Markenaudit',
                'description' => 'Prüfung der Markenkonsistenz',
                'questions' => [
                    [
                        'question_text' => 'Ist das Brand Book im Team verankert?',
                        'weight' => 4,
                        'guidance' => '1=Nein, 5=Vollständig',
                    ],
                    [
                        'question_text' => 'Gibt es einheitliche CI-Richtlinien?',
                        'weight' => 3,
                        'guidance' => '1=Keine Richtlinien, 5=Dokumentiert und geprüft',
                    ],
                ],
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $template = AuditTemplate::where('name', 'Spezial-Markenaudit')->first();
        $this->assertNotNull($template);
        $this->assertEquals($this->module->id, $template->module_id);
        $this->assertCount(2, $template->questions);

        $firstQ = $template->questions->first();
        $this->assertEquals('Ist das Brand Book im Team verankert?', $firstQ->question_text);
        $this->assertEquals(4, $firstQ->weight);
        $this->assertEquals('1=Nein, 5=Vollständig', $firstQ->guidance);
    }

    public function test_user_can_schedule_audit_run_and_conduct_it(): void
    {
        $template = AuditTemplate::create([
            'module_id' => $this->module->id,
            'name' => 'Marken-Audit Vorlage',
            'version' => '1.0',
        ]);
        $question = $template->questions()->create([
            'question_text' => 'Kernfrage Markenwert?',
            'weight' => 5,
            'guidance' => '1 bis 5',
        ]);

        // 1. Schedule the run
        $scheduleResponse = $this->actingAs($this->user)
            ->post(route('actions.audit-run.schedule'), [
                'module_id' => $this->module->id,
                'audit_template_id' => $template->id,
                'auditor_id' => $this->user->id,
                'title' => 'Q4 Marken-Check',
                'cadence' => 'quarterly',
                'due_date' => now()->addDays(10)->format('Y-m-d'),
                'notes' => 'Fokus auf Außenauftritt legen',
            ]);

        $scheduleResponse->assertRedirect(route('dashboard'));
        $scheduleResponse->assertSessionHas('success');

        $run = AuditRun::where('title', 'Q4 Marken-Check')->first();
        $this->assertNotNull($run);
        $this->assertEquals(AuditRun::STATUS_SCHEDULED, $run->status);
        $this->assertEquals('quarterly', $run->cadence);
        $this->assertEquals($this->user->id, $run->auditor_id);

        // 2. Conduct and complete the scheduled run
        $submitResponse = $this->actingAs($this->user)
            ->post(route('actions.audit.submit'), [
                'audit_run_id' => $run->id,
                'module_id' => $this->module->id,
                'audit_template_id' => $template->id,
                'responses' => [
                    $question->id => [
                        'score' => 4,
                        'notes' => 'Sehr gut implementiert',
                    ],
                ],
            ]);

        $submitResponse->assertRedirect(route('dashboard'));
        $submitResponse->assertSessionHas('success');

        $run->refresh();
        $this->assertEquals(AuditRun::STATUS_COMPLETED, $run->status);
        $this->assertNotNull($run->overall_score);
        $this->assertNotNull($run->completed_at);
        $this->assertCount(1, $run->responses);

        // Knowledge edge Measures verified
        $edge = KnowledgeEdge::where('source_type', 'audit')
            ->where('source_id', $run->id)
            ->where('target_id', $this->module->id)
            ->where('relation', 'measures')
            ->first();
        $this->assertNotNull($edge);
    }

    public function test_user_can_create_tool_and_store_content(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('actions.tool.create'), [
                'module_id' => $this->module->id,
                'name' => 'Brand Launch Checkliste',
                'type' => 'checklist',
                'description' => 'Schritt-für-Schritt Launch-Vorbereitung',
                'content' => "1. Logo und Farben festlegen\n2. Styleguide freigeben\n3. Website aktualisieren",
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $tool = Tool::where('name', 'Brand Launch Checkliste')->first();
        $this->assertNotNull($tool);
        $this->assertEquals('checklist', $tool->type);
        $this->assertStringContainsString('Styleguide freigeben', $tool->content);
        $this->assertTrue($tool->is_active);
    }

    public function test_alf_synthesize_validate_and_promote_pipeline(): void
    {
        // 1. Observation
        $obs = Observation::create([
            'title' => 'Kundenfeedback zu Reaktionszeiten',
            'content' => 'Kunden wünschen sich Reaktionszeiten unter 2 Stunden.',
            'created_by' => $this->user->id,
        ]);

        // 2. Synthesize into Learning Draft
        $synthResponse = $this->actingAs($this->user)
            ->post(route('actions.alf.synthesize'), [
                'observation_id' => $obs->id,
                'title' => 'Schnelligkeit schlägt Perfektion im Erstkontakt',
                'summary' => 'Erste Antwort unter 2h erhöht Konvertierung um 40%.',
                'rationale' => 'Messung aus 50 Support-Tickets.',
            ]);

        $synthResponse->assertRedirect(route('dashboard'));
        $learning = Learning::where('title', 'Schnelligkeit schlägt Perfektion im Erstkontakt')->first();
        $this->assertNotNull($learning);
        $this->assertStringContainsString('draft', strtolower($learning->state->name() ?? (string) $learning->state));

        // Observation -(generates)-> Learning verified
        $genEdge = KnowledgeEdge::where('source_type', 'observation')
            ->where('source_id', $obs->id)
            ->where('target_type', 'learning')
            ->where('target_id', $learning->id)
            ->where('relation', 'generates')
            ->first();
        $this->assertNotNull($genEdge);

        // 3. Validate Learning
        $valResponse = $this->actingAs($this->user)
            ->post(route('actions.alf.validate'), [
                'learning_id' => $learning->id,
            ]);

        $valResponse->assertRedirect(route('dashboard'));
        $learning->refresh();
        $this->assertStringContainsString('validated', strtolower($learning->state->name() ?? (string) $learning->state));

        // 4. Promote to Principle and link to Module
        $promoteResponse = $this->actingAs($this->user)
            ->post(route('actions.alf.promote'), [
                'learning_id' => $learning->id,
                'title' => '2-Stunden-Erstkontakt-Prinzip',
                'statement' => 'Jede Kundenanfrage muss innerhalb von 2 Stunden qualifiziert beantwortet werden.',
                'rationale' => 'Basiert auf validiertem Learning.',
                'target_module_id' => $this->module->id,
            ]);

        $promoteResponse->assertRedirect(route('dashboard'));
        $learning->refresh();
        $this->assertStringContainsString('promoted', strtolower($learning->state->name() ?? (string) $learning->state));

        $principle = Principle::where('title', '2-Stunden-Erstkontakt-Prinzip')->first();
        $this->assertNotNull($principle);

        // Principle -(governs)-> Module verified
        $govEdge = KnowledgeEdge::where('source_type', 'principle')
            ->where('source_id', $principle->id)
            ->where('target_type', 'module')
            ->where('target_id', $this->module->id)
            ->where('relation', 'governs')
            ->first();
        $this->assertNotNull($govEdge);
    }

    public function test_dashboard_renders_with_amf_arf_and_alf_workspaces(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('AMF Tool-Studio');
        $response->assertSee('ARF Audit-Center');
        $response->assertSee('ALF Prinzipienschmiede');
        $response->assertSee('auditDesignerModal');
        $response->assertSee('auditScheduleModal');
        $response->assertSee('toolDesignerModal');
        $response->assertSee('toolViewerModal');
    }
}
