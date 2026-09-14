<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Actions\PromoteLearningToPrinciple;
use App\Domains\Knowledge\Actions\ValidateLearning;
use App\Domains\Knowledge\Models\Decision;
use App\Domains\Knowledge\Models\Insight;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Membership;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DosSeedPackSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'disavo'],
            [
                'name'      => 'Disavo Holding GmbH',
                'is_active' => true,
                'settings'  => ['industry' => 'Private Equity & Beteiligungsmanagement'],
            ]
        );

        TenantContext::setTenant($tenant);

        // 2. Create Users
        $owner = User::firstOrCreate(
            ['email' => 'owner@disavo.de'],
            [
                'name'     => 'Disavo Owner',
                'password' => Hash::make('secret123'),
            ]
        );

        $steward = User::firstOrCreate(
            ['email' => 'steward@disavo.de'],
            [
                'name'     => 'Disavo Knowledge Steward',
                'password' => Hash::make('secret123'),
            ]
        );

        $contributor = User::firstOrCreate(
            ['email' => 'contributor@disavo.de'],
            [
                'name'     => 'Disavo Contributor',
                'password' => Hash::make('secret123'),
            ]
        );

        // 3. Memberships
        Membership::firstOrCreate(
            ['tenant_id' => $tenant->id, 'user_id' => $owner->id],
            ['role' => Membership::ROLE_OWNER]
        );

        Membership::firstOrCreate(
            ['tenant_id' => $tenant->id, 'user_id' => $steward->id],
            ['role' => Membership::ROLE_STEWARD]
        );

        Membership::firstOrCreate(
            ['tenant_id' => $tenant->id, 'user_id' => $contributor->id],
            ['role' => Membership::ROLE_CONTRIBUTOR]
        );

        // 4. Configure Spatie Roles per Tenant
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenant->id);
        }

        $ownerRole = Role::firstOrCreate(['name' => 'Owner', 'tenant_id' => $tenant->id]);
        $stewardRole = Role::firstOrCreate(['name' => 'Steward', 'tenant_id' => $tenant->id]);
        $contributorRole = Role::firstOrCreate(['name' => 'Contributor', 'tenant_id' => $tenant->id]);

        $owner->assignRole($ownerRole);
        $steward->assignRole($stewardRole);
        $contributor->assignRole($contributorRole);

        // 5. Canonical Worked Example: The Succession Principle Chain
        // Step 1: Observation
        $observation = Observation::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Kritische Reaktionen bei kurzfristiger Nachfolgeankündigung',
            'content'    => 'Bei der Akquisition von Beteiligung Alpha führten plötzliche Führungswechsel ohne Vorankündigung zu Verunsicherung bei drei A-Kunden und Kündigungsdrohungen im zweiten Führungskreis.',
            'context'    => [
                'beteiligung' => 'Alpha Werkzeugbau',
                'zeitpunkt'   => '2025-Q2',
                'auswirkung'  => 'Umsatzrückgang -14% im Folgequartal',
            ],
            'created_by' => $contributor->id,
        ]);

        // Step 2: Insight
        $insight = Insight::create([
            'tenant_id'    => $tenant->id,
            'title'        => 'Nachfolgeunsicherheit vernichtet kurzfristig Substanzwert',
            'summary'      => 'Reaktive Kommunikation bei Übergaben wird von Schlüsselkunden und Leistungsträgern als Instabilitätsrisiko bewertet.',
            'implications' => 'Übergabekommunikation darf kein Schlusspunkt sein, sondern muss als mehrstufiger Vertrauensprozess geführt werden.',
            'created_by'   => $steward->id,
        ]);

        // Edge: Observation → Insight (Produces)
        $observation->linkTo(
            $insight,
            RelationType::Produces,
            ['confidence' => 95, 'note' => 'Beobachtung liefert empirischen Befund']
        );

        // Step 3: Learning (Formulated and validated)
        $learning = Learning::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Systematische Vorlaufzeit in der Nachfolgekommunikation stabilisiert Kundenbindung',
            'summary'    => 'Ein transparenter, strukturierter Kommunikationsvorlauf von mindestens 12 Monaten vor offizieller Übergabe verhindert Abwanderung und sichert Kontinuität.',
            'rationale'  => 'Vergleich mit Beteiligung Beta zeigte: Frühzeitige Einbindung von Schlüsselkunden bewahrte 100% des Auftragsvolumens.',
            'state'      => Draft::class,
            'created_by' => $contributor->id,
        ]);

        // Edge: Insight → Learning (Validates)
        $insight->linkTo(
            $learning,
            RelationType::Validates,
            ['evidence' => 'Fallstudie Alpha vs Beta']
        );

        // Validate the Learning by Steward
        app(ValidateLearning::class)->handle($learning, $steward);

        // Step 4: Promote Learning to Canonical Principle
        $principle = app(PromoteLearningToPrinciple::class)->handle(
            learning: $learning,
            steward: $steward,
            statement: 'Nachfolgekommunikation beginnt mindestens zwölf Monate vor Übergabe.',
            principleTitle: 'Grundsatz Nachfolgekommunikation',
            rationale: 'Schutz vor Substanzverlust und Wahrung des Vertrauenskapitals bei Schlüsselpartnern.'
        );

        // Step 5: Open Question (Offene Frage)
        $question = Question::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Welche vertraulichen Meilensteine müssen vor der internen Belegschaftskommunikation auditiert sein?',
            'context'    => 'Klärung der Vertraulichkeitsstufen bei M&A-Nachfolgen.',
            'status'     => Question::STATUS_OPEN,
            'created_by' => $steward->id,
        ]);

        // Edge: Learning raises Question
        $learning->linkTo(
            $question,
            RelationType::Raises,
            ['context' => 'Definition der Kommunikationsmeilensteine']
        );

        // Step 6: Decision (Entscheidungspfad)
        $decision = Decision::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Einführung der 12-Monats-Kommunikationspflicht für Beteiligungen',
            'summary'    => 'Alle Portfoliounternehmen müssen 12 Monate vor geplantem Generationenwechsel den DOS-Nachfolgekommunikationsplan aktivieren.',
            'rationale'  => 'Direkte Umsetzung des Prinzips zur Absicherung der Portfoliobewertungen.',
            'decided_at' => now(),
            'decided_by' => $owner->id,
            'created_by' => $owner->id,
        ]);

        // Edge: Decision is JustifiedBy Principle (The "Entscheidungspfad"!)
        $decision->linkTo(
            $principle,
            RelationType::JustifiedBy,
            ['approval' => 'Geschäftsführungsbeschluss Disavo Holding']
        );

        // 6. AMF — The Six Canonical Modules from the Spec (§2.1)
        $modulesData = [
            ['name' => 'Unternehmensentwicklung', 'slug' => 'unternehmensentwicklung', 'order' => 1, 'description' => 'Strategische Ausrichtung, Organisation und Skalierungsstrukturen.'],
            ['name' => 'Markenaufbau',            'slug' => 'markenaufbau',            'order' => 2, 'description' => 'Markenpositionierung, Marktpräsenz und Differenzierung im Wettbewerb.'],
            ['name' => 'Nachfolge',               'slug' => 'nachfolge',               'order' => 3, 'description' => 'Generationswechsel, Übergabeprozesse und Sicherung der Kontinuität.'],
            ['name' => 'Unternehmerentwicklung',  'slug' => 'unternehmerentwicklung',  'order' => 4, 'description' => 'Führungskompetenz, Rollenreflexion und persönliche Weiterentwicklung des Unternehmers.'],
            ['name' => 'Beteiligungsmanagement',  'slug' => 'beteiligungsmanagement',  'order' => 5, 'description' => 'Portfoliosteuerung, Synergienutzung und Wertsteigerungsprogramme.'],
            ['name' => 'Kapitalallokation',       'slug' => 'kapitalallokation',       'order' => 6, 'description' => 'Reinvestitionsstrategie, Liquiditätssteuerung und Renditeoptimierung.'],
        ];

        $createdModules = [];
        foreach ($modulesData as $data) {
            $createdModules[$data['slug']] = \App\Domains\Development\Models\Module::firstOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $data['slug']],
                array_merge($data, ['tenant_id' => $tenant->id])
            );
        }

        $nachfolgeModule = $createdModules['nachfolge'];

        // Graph Edge: Principle GOVERNS Module
        $principle->linkTo(
            $nachfolgeModule,
            RelationType::Governs,
            ['scope' => 'Alle Übergabeprojekte der Holding und Beteiligungsunternehmen']
        );

        // Goal (Zielzustand) for Nachfolge
        $goal = \App\Domains\Development\Models\Goal::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $nachfolgeModule->id,
            'title'        => 'Zielzustand Reifegrad Nachfolge',
            'description'  => 'Vollständig strukturierter Nachfolgeprozess mit 12-Monats-Kommunikation und verifiziertem Risikoprofil.',
            'target_score' => 95.00,
            'version'      => 1,
            'created_by'   => $owner->id,
        ]);

        // Starter Audit Template on Nachfolge
        $auditTemplate = \App\Domains\Development\Models\AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $nachfolgeModule->id,
            'name'        => 'Nachfolge-Reifegrad-Audit',
            'description' => 'Systematische Überprüfung der Vorbereitungs- und Kommunikationsreife vor Übergaben.',
        ]);

        $q1 = \App\Domains\Development\Models\AuditQuestion::create([
            'audit_template_id' => $auditTemplate->id,
            'question_text'     => 'Ist ein verbindlicher Kommunikationsplan mindestens 12 Monate vor dem Stichtag fixiert?',
            'guidance'          => 'Prüfung anhand des dokumentierten Projektplans.',
            'weight'            => 20, // Database weight
            'max_score'         => 5,
            'order'             => 1,
        ]);

        $q2 = \App\Domains\Development\Models\AuditQuestion::create([
            'audit_template_id' => $auditTemplate->id,
            'question_text'     => 'Wurden die Top-A-Kunden in vertrauliche Vorgespräche eingebunden?',
            'guidance'          => 'Nachweis über persönliche Geschäftsführungskontakte.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 2,
        ]);

        $q3 = \App\Domains\Development\Models\AuditQuestion::create([
            'audit_template_id' => $auditTemplate->id,
            'question_text'     => 'Ist das Nachfolgeprofil für Schlüsselpositionen im Führungskreis verabschiedet?',
            'guidance'          => 'Stellenbeschreibungen und Anforderungsprofile.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 3,
        ]);

        // Execute & Score Initial Audit Run
        $auditRun = app(\App\Domains\Development\Actions\RunAudit::class)->handle(
            module: $nachfolgeModule,
            template: $auditTemplate,
            auditor: $steward
        );

        app(\App\Domains\Development\Actions\ScoreAudit::class)->handle(
            run: $auditRun,
            responses: [
                $q1->id => ['score' => 4, 'notes' => '12-Monats-Kommunikationsplan vorhanden und aktiv.'],
                $q2->id => ['score' => 4, 'notes' => 'Top-5 Kunden vorab informiert.'],
                $q3->id => ['score' => 3, 'notes' => 'Zweite Führungsebene noch in Vorbereitung.'],
            ]
        );

        // KPI on Nachfolge
        $kpi = \App\Domains\Development\Models\Kpi::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $nachfolgeModule->id,
            'name'         => 'Kundenretentionsrate bei Generationenwechsel',
            'code'         => 'KPI_RETENTION_SUCCESSION',
            'unit'         => '%',
            'direction'    => 'higher_is_better',
            'target_value' => 95.0,
        ]);

        app(\App\Domains\Development\Actions\RecordKpiReading::class)->handle(
            kpi: $kpi,
            value: 92.5,
            recorder: $steward,
            notes: 'Erstes Quartal nach Nachfolgeübergabe Alpha Werkzeugbau'
        );

        // Tool on Nachfolge
        $tool = \App\Domains\Development\Models\Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $nachfolgeModule->id,
            'name'        => 'DOS Nachfolge-Kommunikationsmatrix',
            'type'        => \App\Domains\Development\Models\Tool::TYPE_CHECKLIST,
            'url_or_path' => '/templates/nachfolge_matrix_v1.pdf',
            'description' => 'Standardisierte Checkliste für 12-Monats-Meilensteine bei Übergaben.',
        ]);

        // Tool IMPROVES Module
        $tool->linkTo(
            $nachfolgeModule,
            RelationType::Improves,
            ['impact' => 'Reduziert Vorbereitungszeit um 40%']
        );

        // ARF — Review Cycle Closing the Loop
        $review = \App\Domains\Review\Models\Review::create([
            'tenant_id'   => $tenant->id,
            'title'       => 'Q1-2026 Portfolio-Reifegrad-Review',
            'period'      => 'Q1-2026',
            'review_date' => now()->subDays(5)->toDateString(),
            'status'      => \App\Domains\Review\Models\Review::STATUS_OPEN,
            'summary'     => 'Prüfung der Nachfolgereife und operativen Stabilität der Beteiligungen.',
            'created_by'  => $owner->id,
        ]);

        $reviewItem = \App\Domains\Review\Models\ReviewItem::create([
            'review_id' => $review->id,
            'module_id' => $nachfolgeModule->id,
            'topic'     => 'Transparenz in der Mitarbeiterkommunikation bei Nachfolgen',
            'status'    => 'identified',
            'notes'     => 'Führungskräfte der zweiten Ebene benötigen spezifische Schulungen vor externer Ankündigung.',
        ]);

        $improvement = \App\Domains\Review\Models\Improvement::create([
            'review_id'      => $review->id,
            'review_item_id' => $reviewItem->id,
            'title'          => 'Schulungsworkshop für zweite Führungsebene einführen',
            'action_plan'    => 'Erarbeitung eines 2-Tages-Trainings für Führungskräfte zur Begleitung der Übergabe.',
            'owner_id'       => $steward->id,
            'due_date'       => now()->addDays(30)->toDateString(),
            'status'         => \App\Domains\Review\Models\Improvement::STATUS_PENDING,
        ]);
    }
}
