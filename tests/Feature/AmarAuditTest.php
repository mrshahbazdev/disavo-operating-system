<?php

namespace Tests\Feature;

use App\Domains\Development\Models\AuditQuestion;
use App\Domains\Development\Models\AuditResponse;
use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Services\AmarTemplateService;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AmarAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $user;
    protected Module $module;
    protected AuditTemplate $amarTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'disavo'],
            ['name' => 'Disavo Holding GmbH']
        );
        TenantContext::setTenant($this->tenant);

        $this->user = User::firstOrCreate(
            ['email' => 'amar.auditor@disavo.test'],
            ['name' => 'AMAR Auditor', 'password' => bcrypt('secret')]
        );

        $this->module = Module::where('slug', 'unternehmensentwicklung')->first()
            ?? Module::firstOrFail();

        $service = app(AmarTemplateService::class);
        $this->amarTemplate = $service->ensureAmarTemplate($this->tenant, $this->module);
    }

    public function test_canonical_amar_template_exists_with_ten_areas_and_questions(): void
    {
        $this->assertEquals(AmarTemplateService::TEMPLATE_NAME, $this->amarTemplate->name);
        $this->assertGreaterThanOrEqual(50, $this->amarTemplate->questions()->count());

        $areas = $this->amarTemplate->questions()->pluck('area')->unique()->values()->all();
        $this->assertCount(10, $areas);
        $this->assertContains('AREA 1: ONBOARDING', $areas);
        $this->assertContains('AREA 2: AUDIT', $areas);
        $this->assertContains('AREA 3: EVALUATION', $areas);
        $this->assertContains('AREA 4: AI COACH', $areas);
        $this->assertContains('AREA 5: BOOKS', $areas);
        $this->assertContains('AREA 6: TOOL RECOMMENDATIONS', $areas);
        $this->assertContains('AREA 7: TOOL AUDIT', $areas);
        $this->assertContains('AREA 8: IMPLEMENTATION', $areas);
        $this->assertContains('AREA 9: PROGRESS', $areas);
        $this->assertContains('AREA 10: ENTHUSIASM', $areas);

        $binaryQuestions = $this->amarTemplate->questions()->where('type', 'binary')->get();
        $this->assertGreaterThanOrEqual(4, $binaryQuestions->count());
        foreach ($binaryQuestions as $bq) {
            $this->assertTrue($bq->isBinary());
            $this->assertEquals(1, $bq->max_score);
        }
    }

    public function test_submit_amar_audit_with_entrepreneur_test_and_area_breakdown_scoring(): void
    {
        $this->actingAs($this->user);

        $responses = [];
        foreach ($this->amarTemplate->questions as $q) {
            if ($q->isBinary()) {
                $responses[$q->id] = [
                    'binary_answer' => 'yes',
                    'status_symbol' => 'usable',
                    'notes'         => 'Schlüsseltest erfüllt',
                ];
            } else {
                $responses[$q->id] = [
                    'score'         => 4,
                    'status_symbol' => 'usable',
                    'notes'         => 'Gute Implementierung',
                ];
            }
        }

        $payload = [
            'module_id'                => $this->module->id,
            'audit_template_id'        => $this->amarTemplate->id,
            'version'                  => '1.0',
            'user_paths_audited'       => 2,
            'tools_audited'            => 6,
            'previous_score'           => 70,
            'entrepreneur_test_passed' => 1,
            'responses'                => $responses,
            'insight_protocols'        => [
                [
                    'area'          => 'AREA 1: ONBOARDING',
                    'problem'       => 'Call to Action Button auf Mobile schwer klickbar',
                    'observation'   => 'Padding auf Touchscreens zu gering',
                    'impact'        => 'Verzögerung beim Erstkontakt',
                    'severity'      => 6,
                    'affected_users'=> 'Mobile Nutzer',
                    'responsible'   => 'Frontend Lead',
                    'action'        => 'Touch-Target auf mind. 44px vergrößern',
                    'expected_benefit' => 'Höhere Onboarding-Konversionsrate',
                    'metric'        => 'Abschlussquote Onboarding > 85%',
                ]
            ],
            'cybernetic_feedback'      => [
                'highest_leverage'   => 'Onboarding und Klarheit des Value Propositions',
                'tools_to_simplify'  => 'Tool-Navigator übersichtlicher gliedern',
                'user_path_dropout'  => 'Nach erstem Audit-Ergebnis',
                'surprising_insight' => 'Sehr hohe Nachfrage nach konkreten Umsetzungs-Checklisten',
                'main_bottleneck'    => 'Priorisierung der nächsten Entwicklungsstufe',
                'top_priorities'     => '1. Onboarding CTA optimieren; 2. Mobile Usability; 3. Insight-Tracking schärfen',
                'more_valuable_today'=> 'yes',
            ],
        ];

        $response = $this->post(route('actions.audit.submit'), $payload);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $latestRun = AuditRun::where('tenant_id', $this->tenant->id)
            ->where('audit_template_id', $this->amarTemplate->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertEquals(AuditRun::STATUS_COMPLETED, $latestRun->status);
        $this->assertTrue($latestRun->isAmar());
        $this->assertTrue((bool) $latestRun->entrepreneur_test_passed);
        $this->assertFalse((bool) $latestRun->new_feature_ban);
        $this->assertGreaterThan(70, $latestRun->score);

        $meta = $latestRun->meta;
        $this->assertIsArray($meta);
        $this->assertEquals('1.0', $meta['version']);
        $this->assertEquals(2, $meta['user_paths_audited']);
        $this->assertEquals(6, $meta['tools_audited']);
        $this->assertEquals(70, $meta['previous_score']);
        $this->assertNotEmpty($meta['area_scores']);
        $this->assertCount(10, $meta['area_scores']);

        $this->assertCount(1, $meta['insight_protocols']);
        $this->assertEquals('Call to Action Button auf Mobile schwer klickbar', $meta['insight_protocols'][0]['problem']);

        $this->assertEquals('yes', $meta['cybernetic_feedback']['more_valuable_today']);
    }

    public function test_cybernetic_gate_negative_triggers_new_feature_ban(): void
    {
        $this->actingAs($this->user);

        $responses = [];
        foreach ($this->amarTemplate->questions as $q) {
            if ($q->isBinary()) {
                $responses[$q->id] = [
                    'binary_answer' => 'no',
                    'status_symbol' => 'defect',
                ];
            } else {
                $responses[$q->id] = [
                    'score'         => 1,
                    'status_symbol' => 'defect',
                ];
            }
        }

        $payload = [
            'module_id'                => $this->module->id,
            'audit_template_id'        => $this->amarTemplate->id,
            'version'                  => '1.0',
            'user_paths_audited'       => 1,
            'tools_audited'            => 4,
            'previous_score'           => 65,
            'entrepreneur_test_passed' => 0,
            'responses'                => $responses,
            'cybernetic_feedback'      => [
                'more_valuable_today'  => 'no', // STOP-RULE! Triggers New Feature Ban
                'highest_leverage'     => 'Stopp neuer Features',
            ],
        ];

        $response = $this->post(route('actions.audit.submit'), $payload);
        $response->assertRedirect(route('dashboard'));

        $latestRun = AuditRun::where('tenant_id', $this->tenant->id)
            ->where('audit_template_id', $this->amarTemplate->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertTrue((bool) $latestRun->new_feature_ban);
        $this->assertTrue($latestRun->hasFeatureBan());
    }

    public function test_dashboard_displays_feature_ban_alert_and_amar_runs(): void
    {
        // 1. Create a run with feature ban active
        AuditRun::create([
            'tenant_id'                => $this->tenant->id,
            'module_id'                => $this->module->id,
            'audit_template_id'        => $this->amarTemplate->id,
            'conducted_by'             => $this->user->id,
            'status'                   => AuditRun::STATUS_COMPLETED,
            'score'                    => 42.0,
            'version'                  => '1.0',
            'new_feature_ban'          => true,
            'entrepreneur_test_passed' => false,
            'completed_at'             => now(),
            'meta'                     => [
                'new_feature_ban'      => true,
                'cybernetic_feedback'  => [
                    'more_valuable_today' => 'no',
                ],
            ],
        ]);

        $this->actingAs($this->user);
        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('NEW FEATURE BAN AKTIV');
        $response->assertSee('ALLOCORE MASTER AUDIT (AMAR)');
        $response->assertSee('Allocore Value Score');
    }

    public function test_amar_ensure_endpoint(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('actions.audit-template.ensure-amar'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('audit_templates', [
            'tenant_id' => $this->tenant->id,
            'name'      => AmarTemplateService::TEMPLATE_NAME,
        ]);
    }
}
