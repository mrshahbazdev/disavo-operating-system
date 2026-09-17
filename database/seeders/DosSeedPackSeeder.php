<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Development\Actions\RecordKpiReading;
use App\Domains\Development\Actions\RunAudit;
use App\Domains\Development\Actions\ScoreAudit;
use App\Domains\Development\Models\AuditQuestion;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Actions\PromoteLearningToPrinciple;
use App\Domains\Knowledge\Actions\ValidateLearning;
use App\Domains\Knowledge\Models\Decision;
use App\Domains\Knowledge\Models\Insight;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Knowledge\States\Principle\Active;
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

        // AMF 1.1 Meta-Governance Principle AP-005
        $ap005Principle = Principle::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Prinzip AP-005: Das Allocore-Modul-Prinzip'],
            [
                'statement'  => 'Ein Modul ist kein Audit. Ein Modul besteht aus: Audit, Messregeln, KPIs, Werkzeugen, Learnings und Reviews.',
                'rationale'  => 'Das AMF (Allocore Module Framework) 1.1 ist die Modulfabrik von Allocore. Bevor Module entwickelt werden, definiert das AMF als standardisierte Blaupause, wie Module entstehen: 1. Entwicklungsobjekt, 2. messbare Zieldefinition, 3. die 5 Allocore-Ebenen (Umsatz, Gewinn, Ordnung, Einfluss, Vermächtnis), 4. Audit, 5. KPI-System, 6. Werkzeuge, 7. Learnings (ALF), 8. Review-Zyklus (ARF) und 9. Versionierung.',
                'state'      => Active::class,
                'version'    => 1,
                'created_by' => $owner->id,
            ]
        );
        $ap005Principle->linkTo(
            $createdModules['unternehmensentwicklung'],
            RelationType::Governs,
            ['scope' => 'AMF 1.1 Meta-Blaupause für alle kanonischen Module']
        );

        // -------------------------------------------------------------
        // MODULE 1: UNTERNEHMENSENTWICKLUNG
        // -------------------------------------------------------------
        $ueModule = $createdModules['unternehmensentwicklung'];

        $uePrinciple = Principle::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Subsidiarität der operativen Entscheidungen',
            'statement'  => 'Operative Entscheidungen werden auf der untersten kompetenten Ebene getroffen, um Reaktionszeiten zu minimieren und Skalierungsengpässe zu vermeiden.',
            'rationale'  => 'Vermeidung von Führungsengpässen und Förderung eigenverantwortlicher Prozesse im Wachstum.',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $owner->id,
        ]);
        $uePrinciple->linkTo(
            $ueModule,
            RelationType::Governs,
            ['scope' => 'Organisationsstrukturen, Prozesse und Delegation in allen Portfoliounternehmen']
        );

        Goal::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $ueModule->id,
            'title'        => 'Zielzustand Skalierbare Organisationsarchitektur',
            'description'  => 'Dezentrale Entscheidungsfindung mit dokumentierten Standard Operating Procedures (SOPs) und auditierter Prozessreife.',
            'target_score' => 90.00,
            'version'      => 1,
            'created_by'   => $owner->id,
        ]);

        $ueAuditTemplate = AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $ueModule->id,
            'name'        => 'Unternehmensentwicklungs-Reifegrad-Audit',
            'description' => 'Ganzheitliche Überprüfung von Organisationsstruktur, Prozessdokumentation und Skalierungsfähigkeit.',
        ]);

        $ueQ1 = AuditQuestion::create([
            'audit_template_id' => $ueAuditTemplate->id,
            'question_text'     => 'Sind alle Kernprozesse dokumentiert und ohne Schlüsselpersonen-Abhängigkeit replizierbar?',
            'guidance'          => 'Prüfung anhand digitaler SOPs und Vertretungsregelungen.',
            'weight'            => 20,
            'max_score'         => 5,
            'order'             => 1,
        ]);

        $ueQ2 = AuditQuestion::create([
            'audit_template_id' => $ueAuditTemplate->id,
            'question_text'     => 'Existiert ein klares Kompetenz- und Freigabematrix-System für Budgets und Einstellungen?',
            'guidance'          => 'Prüfung des Unterschriften- und Freigabekatalogs.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 2,
        ]);

        $ueQ3 = AuditQuestion::create([
            'audit_template_id' => $ueAuditTemplate->id,
            'question_text'     => 'Werden Quartals-Reviews der Organisations- und Skalierungsziele verbindlich umgesetzt?',
            'guidance'          => 'Protokolle der strategischen Führungskreis-Sitzungen.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 3,
        ]);

        $ueRun = app(RunAudit::class)->handle(
            module: $ueModule,
            template: $ueAuditTemplate,
            auditor: $steward
        );

        app(ScoreAudit::class)->handle(
            run: $ueRun,
            responses: [
                $ueQ1->id => ['score' => 4, 'notes' => 'SOPs für 85% der Kernprozesse vollständig hinterlegt.'],
                $ueQ2->id => ['score' => 5, 'notes' => 'Freigabematrix digital verankert und auditfest.'],
                $ueQ3->id => ['score' => 4, 'notes' => 'Quartals-Reviews finden im Rhythmus statt.'],
            ]
        );

        $ueKpi = Kpi::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $ueModule->id,
            'name'         => 'Prozessautomatisierungs- & Dokumentationsgrad',
            'code'         => 'KPI_PROCESS_MATURITY',
            'unit'         => '%',
            'direction'    => 'higher_is_better',
            'target_value' => 85.0,
        ]);

        app(RecordKpiReading::class)->handle(
            kpi: $ueKpi,
            value: 82.5,
            recorder: $steward,
            notes: 'Audit der Kernprozesse im Portfolio Q1'
        );

        $ueTool = Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $ueModule->id,
            'name'        => 'DOS Skalierungs- & Organisationshandbuch',
            'type'        => Tool::TYPE_TEMPLATE,
            'url_or_path' => '/templates/unternehmensentwicklung_sop_v1.pdf',
            'description' => 'Standardisierte Dokumentationsrichtlinie für skalierbare Aufbau- und Ablauforganisationen.',
        ]);

        $ueTool->linkTo(
            $ueModule,
            RelationType::Improves,
            ['impact' => 'Verkürzt Einarbeitungszeiten um 35% und eliminiert Wissensträger-Monopole']
        );

        // -------------------------------------------------------------
        // MODULE 2: MARKENAUFBAU
        // -------------------------------------------------------------
        $maModule = $createdModules['markenaufbau'];

        $maPrinciple = Principle::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Markenkraft schützt Preissetzungsmacht',
            'statement'  => 'Eine konsistente, differenzierte Markenpositionierung sichert Margenstabilität und Unabhängigkeit von aggressivem Preiswettbewerb.',
            'rationale'  => 'Preisführerschaft erfordert immaterielle Differenzierung über Marke und Vertrauenskapital.',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $owner->id,
        ]);
        $maPrinciple->linkTo(
            $maModule,
            RelationType::Governs,
            ['scope' => 'Markenführung, Marketing und Positionierung über alle Gruppenunternehmen']
        );

        Goal::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $maModule->id,
            'title'        => 'Zielzustand Premium-Markenpositionierung',
            'description'  => 'Etablierte Marktpräsenz als Qualitäts- und Technologieführer mit überdurchschnittlicher Preissetzungskraft.',
            'target_score' => 85.00,
            'version'      => 1,
            'created_by'   => $owner->id,
        ]);

        $maAuditTemplate = AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $maModule->id,
            'name'        => 'Marken- & Positionierungs-Audit',
            'description' => 'Messung von Markenkonsistenz, Kundentreue und Differenzierungsgüte.',
        ]);

        $maQ1 = AuditQuestion::create([
            'audit_template_id' => $maAuditTemplate->id,
            'question_text'     => 'Ist das Brand-Value-Proposition-Profil im Vertrieb verbindlich verankert und geschult?',
            'guidance'          => 'Vertriebsmaterialien, Pitch Decks und Verkaufsgesprächs-Leitfäden.',
            'weight'            => 20,
            'max_score'         => 5,
            'order'             => 1,
        ]);

        $maQ2 = AuditQuestion::create([
            'audit_template_id' => $maAuditTemplate->id,
            'question_text'     => 'Wird das Corporate Identity Design über alle digitalen und physischen Touchpoints eingehalten?',
            'guidance'          => 'Prüfung von Website, Angeboten, Messeauftritten und Social Media.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 2,
        ]);

        $maQ3 = AuditQuestion::create([
            'audit_template_id' => $maAuditTemplate->id,
            'question_text'     => 'Erfolgt eine jährliche Marktwahrnehmungs- und Wettbewerberanalyse mit Kundenbefragung?',
            'guidance'          => 'Auswertung der Kundenfeedbacks und NPS-Erhebungen.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 3,
        ]);

        $maRun = app(RunAudit::class)->handle(
            module: $maModule,
            template: $maAuditTemplate,
            auditor: $steward
        );

        app(ScoreAudit::class)->handle(
            run: $maRun,
            responses: [
                $maQ1->id => ['score' => 4, 'notes' => 'Vertriebsschulung für B2B-Value-Proposition erfolgreich durchgeführt.'],
                $maQ2->id => ['score' => 4, 'notes' => 'CI-Richtlinien zu 90% in allen Kanälen konsistent umgesetzt.'],
                $maQ3->id => ['score' => 4, 'notes' => 'Kundenbefragung Q4 abgeschlossen.'],
            ]
        );

        $maKpi = Kpi::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $maModule->id,
            'name'         => 'Net Promoter Score (NPS)',
            'code'         => 'KPI_BRAND_NPS',
            'unit'         => 'pts',
            'direction'    => 'higher_is_better',
            'target_value' => 65.0,
        ]);

        app(RecordKpiReading::class)->handle(
            kpi: $maKpi,
            value: 62.0,
            recorder: $steward,
            notes: 'B2B-Kundenpanel Auswertung 2026'
        );

        $maTool = Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $maModule->id,
            'name'        => 'DOS Brand Identity & Voice Guideline',
            'type'        => Tool::TYPE_WHITEPAPER,
            'url_or_path' => '/templates/brand_identity_guideline.pdf',
            'description' => 'Umfassendes Handbuch für Tonalität, visuelle Identität und Markenarchitektur.',
        ]);

        $maTool->linkTo(
            $maModule,
            RelationType::Improves,
            ['impact' => 'Sichert einheitlichen Markenauftritt und stärkt Preissetzungskompetenz']
        );

        // -------------------------------------------------------------
        // MODULE 3: NACHFOLGE
        // -------------------------------------------------------------
        $nachfolgeModule = $createdModules['nachfolge'];

        // Graph Edge: Principle GOVERNS Module
        $principle->linkTo(
            $nachfolgeModule,
            RelationType::Governs,
            ['scope' => 'Alle Übergabeprojekte der Holding und Beteiligungsunternehmen']
        );

        // Goal (Zielzustand) for Nachfolge
        Goal::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $nachfolgeModule->id,
            'title'        => 'Zielzustand Reifegrad Nachfolge',
            'description'  => 'Vollständig strukturierter Nachfolgeprozess mit 12-Monats-Kommunikation und verifiziertem Risikoprofil.',
            'target_score' => 95.00,
            'version'      => 1,
            'created_by'   => $owner->id,
        ]);

        // Starter Audit Template on Nachfolge
        $auditTemplate = AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $nachfolgeModule->id,
            'name'        => 'Nachfolge-Reifegrad-Audit',
            'description' => 'Systematische Überprüfung der Vorbereitungs- und Kommunikationsreife vor Übergaben.',
        ]);

        $q1 = AuditQuestion::create([
            'audit_template_id' => $auditTemplate->id,
            'question_text'     => 'Ist ein verbindlicher Kommunikationsplan mindestens 12 Monate vor dem Stichtag fixiert?',
            'guidance'          => 'Prüfung anhand des dokumentierten Projektplans.',
            'weight'            => 20, // Database weight
            'max_score'         => 5,
            'order'             => 1,
        ]);

        $q2 = AuditQuestion::create([
            'audit_template_id' => $auditTemplate->id,
            'question_text'     => 'Wurden die Top-A-Kunden in vertrauliche Vorgespräche eingebunden?',
            'guidance'          => 'Nachweis über persönliche Geschäftsführungskontakte.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 2,
        ]);

        $q3 = AuditQuestion::create([
            'audit_template_id' => $auditTemplate->id,
            'question_text'     => 'Ist das Nachfolgeprofil für Schlüsselpositionen im Führungskreis verabschiedet?',
            'guidance'          => 'Stellenbeschreibungen und Anforderungsprofile.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 3,
        ]);

        // Execute & Score Initial Audit Run
        $auditRun = app(RunAudit::class)->handle(
            module: $nachfolgeModule,
            template: $auditTemplate,
            auditor: $steward
        );

        app(ScoreAudit::class)->handle(
            run: $auditRun,
            responses: [
                $q1->id => ['score' => 4, 'notes' => '12-Monats-Kommunikationsplan vorhanden und aktiv.'],
                $q2->id => ['score' => 4, 'notes' => 'Top-5 Kunden vorab informiert.'],
                $q3->id => ['score' => 3, 'notes' => 'Zweite Führungsebene noch in Vorbereitung.'],
            ]
        );

        // KPI on Nachfolge
        $kpi = Kpi::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $nachfolgeModule->id,
            'name'         => 'Kundenretentionsrate bei Generationenwechsel',
            'code'         => 'KPI_RETENTION_SUCCESSION',
            'unit'         => '%',
            'direction'    => 'higher_is_better',
            'target_value' => 95.0,
        ]);

        app(RecordKpiReading::class)->handle(
            kpi: $kpi,
            value: 92.5,
            recorder: $steward,
            notes: 'Erstes Quartal nach Nachfolgeübergabe Alpha Werkzeugbau'
        );

        // Tool on Nachfolge
        $tool = Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $nachfolgeModule->id,
            'name'        => 'DOS Nachfolge-Kommunikationsmatrix',
            'type'        => Tool::TYPE_CHECKLIST,
            'url_or_path' => '/templates/nachfolge_matrix_v1.pdf',
            'description' => 'Standardisierte Checkliste für 12-Monats-Meilensteine bei Übergaben.',
        ]);

        // Tool IMPROVES Module
        $tool->linkTo(
            $nachfolgeModule,
            RelationType::Improves,
            ['impact' => 'Reduziert Vorbereitungszeit um 40%']
        );

        // -------------------------------------------------------------
        // MODULE 4: UNTERNEHMERENTWICKLUNG
        // -------------------------------------------------------------
        $ueDevModule = $createdModules['unternehmerentwicklung'];

        $ueDevPrinciple = Principle::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Unternehmerischer Fokus auf Strategie und Portfolio',
            'statement'  => 'Die Hauptaufgabe des Unternehmers liegt in Strategie, Kapitalallokation und Führungskräfteentwicklung – nicht im operativen Abarbeiten.',
            'rationale'  => 'Operative Überlastung des Gründers/Inhabers blockiert strategische Expansion und erhöht Klumpenrisiken.',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $owner->id,
        ]);
        $ueDevPrinciple->linkTo(
            $ueDevModule,
            RelationType::Governs,
            ['scope' => 'Führungskräfteentwicklung, Zeitallokation und Rollenprofil der Geschäftsführung']
        );

        Goal::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $ueDevModule->id,
            'title'        => 'Zielzustand Operative Unabhängigkeit',
            'description'  => 'Vollständige Befreiung des Unternehmers vom operativen Tagesgeschäft bei maximal 20% operativer Zeitbindung.',
            'target_score' => 85.00,
            'version'      => 1,
            'created_by'   => $owner->id,
        ]);

        $ueDevAuditTemplate = AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $ueDevModule->id,
            'name'        => 'Führungskapazitäts- & Delegations-Audit',
            'description' => 'Evaluierung der unternehmerischen Zeitallokation, Delegationsdisziplin und Führungsautonomie.',
        ]);

        $ueDevQ1 = AuditQuestion::create([
            'audit_template_id' => $ueDevAuditTemplate->id,
            'question_text'     => 'Ist der Unternehmer in operativen Kunden- und Projektabläufen vollständig ersetzbar?',
            'guidance'          => 'Prüfung der Ausfallzeit-Sicherheit (z.B. 4-Wochen-Abwesenheitstest).',
            'weight'            => 20,
            'max_score'         => 5,
            'order'             => 1,
        ]);

        $ueDevQ2 = AuditQuestion::create([
            'audit_template_id' => $ueDevAuditTemplate->id,
            'question_text'     => 'Existiert ein strukturierter Coaching- und Weiterbildungsplan für die Geschäftsleitung?',
            'guidance'          => 'Dokumentierte Entwicklungsziele und externe Mentoring-Programme.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 2,
        ]);

        $ueDevQ3 = AuditQuestion::create([
            'audit_template_id' => $ueDevAuditTemplate->id,
            'question_text'     => 'Werden wöchentliche strategische Fokus-Tage verbindlich von Terminen freigehalten?',
            'guidance'          => 'Kalenderanalyse der letzten 12 Wochen.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 3,
        ]);

        $ueDevRun = app(RunAudit::class)->handle(
            module: $ueDevModule,
            template: $ueDevAuditTemplate,
            auditor: $steward
        );

        app(ScoreAudit::class)->handle(
            run: $ueDevRun,
            responses: [
                $ueDevQ1->id => ['score' => 4, 'notes' => '4-Wochen-Abwesenheitstest im Sommer erfolgreich bestanden.'],
                $ueDevQ2->id => ['score' => 4, 'notes' => 'Executive Coaching für C-Level etabliert.'],
                $ueDevQ3->id => ['score' => 3, 'notes' => 'Fokus-Tage werden gelegentlich durch Dringendes unterbrochen.'],
            ]
        );

        $ueDevKpi = Kpi::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $ueDevModule->id,
            'name'         => 'Operative Zeitbindung des Unternehmers',
            'code'         => 'KPI_OWNER_OPERATIONAL_LOAD',
            'unit'         => '%',
            'direction'    => 'lower_is_better',
            'target_value' => 20.0,
        ]);

        app(RecordKpiReading::class)->handle(
            kpi: $ueDevKpi,
            value: 22.5,
            recorder: $steward,
            notes: 'Zeiterfassung Q1: Noch 22.5% operative Tätigkeiten'
        );

        $ueDevTool = Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $ueDevModule->id,
            'name'        => 'Delegations-Framework & Zeit-Audit Matrix',
            'type'        => Tool::TYPE_CHECKLIST,
            'url_or_path' => '/templates/delegation_framework_v1.pdf',
            'description' => 'Werkzeug zur Analyse von Zeitallokation und systematischer Aufgabenabgabe an die zweite Führungsebene.',
        ]);

        $ueDevTool->linkTo(
            $ueDevModule,
            RelationType::Improves,
            ['impact' => 'Senkt operative Involvierung um durchschnittlich 15 Wochenstunden']
        );

        // -------------------------------------------------------------
        // MODULE 5: BETEILIGUNGSMANAGEMENT
        // -------------------------------------------------------------
        $bmModule = $createdModules['beteiligungsmanagement'];

        $bmPrinciple = Principle::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Aktive Beteiligungssteuerung nach Wertsteigerungshebeln',
            'statement'  => 'Beteiligungsunternehmen werden durch verbindliche KPI-Systeme und strukturierte Wertsteigerungsinitiativen aktiv gesteuert.',
            'rationale'  => 'Passives Halten mindert Synergiepotenziale und verzögert das Erkennen operativer Fehlentwicklungen.',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $owner->id,
        ]);
        $bmPrinciple->linkTo(
            $bmModule,
            RelationType::Governs,
            ['scope' => 'Portfolioüberwachung, monatliches Reporting und Beiratsarbeit']
        );

        Goal::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $bmModule->id,
            'title'        => 'Zielzustand Integriertes Beteiligungscontrolling',
            'description'  => 'Einheitliches Reporting, automatisierte Monatsabschlüsse und quartalsweise Reifegrad-Audits für alle Tochtergesellschaften.',
            'target_score' => 90.00,
            'version'      => 1,
            'created_by'   => $owner->id,
        ]);

        $bmAuditTemplate = AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $bmModule->id,
            'name'        => 'Portfolio-Governance- & Beteiligungs-Audit',
            'description' => 'Systematische Überprüfung von Berichtsdisziplin, Risikosteuerung und Wertsteigerungsmaßnahmen.',
        ]);

        $bmQ1 = AuditQuestion::create([
            'audit_template_id' => $bmAuditTemplate->id,
            'question_text'     => 'Liegen standardisierte Monats- und Quartalsabschlüsse bis zum 10. Werktag vollständig vor?',
            'guidance'          => 'Einhaltung der Reporting-Deadlines und Konsolidierungsfähigkeit.',
            'weight'            => 20,
            'max_score'         => 5,
            'order'             => 1,
        ]);

        $bmQ2 = AuditQuestion::create([
            'audit_template_id' => $bmAuditTemplate->id,
            'question_text'     => 'Sind operative Wertsteigerungsprogramme definiert und mit Meilensteinen hinterlegt?',
            'guidance'          => 'Fortschrittsberichte zu Effizienz- und Digitalisierungsinitiativen.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 2,
        ]);

        $bmQ3 = AuditQuestion::create([
            'audit_template_id' => $bmAuditTemplate->id,
            'question_text'     => 'Gibt es ein wirksames Frühwarn- und Risikomonitoring auf Ebene der Tochtergesellschaften?',
            'guidance'          => 'Überprüfung des vierteljährlichen Risikoberichts.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 3,
        ]);

        $bmRun = app(RunAudit::class)->handle(
            module: $bmModule,
            template: $bmAuditTemplate,
            auditor: $steward
        );

        app(ScoreAudit::class)->handle(
            run: $bmRun,
            responses: [
                $bmQ1->id => ['score' => 5, 'notes' => 'Reporting-Disziplin über alle Einheiten eingehalten.'],
                $bmQ2->id => ['score' => 4, 'notes' => 'Wertsteigerungsprojekte im Zeitplan.'],
                $bmQ3->id => ['score' => 4, 'notes' => 'Risikofrüherkennung mit Quartals-Scorecards etabliert.'],
            ]
        );

        $bmKpi = Kpi::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $bmModule->id,
            'name'         => 'Portfolio-EBITDA-Wachstum (YoY)',
            'code'         => 'KPI_PORTFOLIO_EBITDA_GROWTH',
            'unit'         => '%',
            'direction'    => 'higher_is_better',
            'target_value' => 15.0,
        ]);

        app(RecordKpiReading::class)->handle(
            kpi: $bmKpi,
            value: 14.8,
            recorder: $steward,
            notes: 'Konsolidiertes Wachstum über alle Portfolio-Unternehmen'
        );

        $bmTool = Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $bmModule->id,
            'name'        => 'Beteiligungs-Quartalsbericht Template',
            'type'        => Tool::TYPE_TEMPLATE,
            'url_or_path' => '/templates/portfolio_quarterly_report.xlsx',
            'description' => 'Standardisierte Finanz- und Reifegrad-Berichterstattung für Beteiligungsunternehmen.',
        ]);

        $bmTool->linkTo(
            $bmModule,
            RelationType::Improves,
            ['impact' => 'Beschleunigt den Monatsabschluss und vereinheitlicht KPI-Vergleiche']
        );

        // -------------------------------------------------------------
        // MODULE 6: KAPITALALLOKATION
        // -------------------------------------------------------------
        $kaModule = $createdModules['kapitalallokation'];

        $kaPrinciple = Principle::create([
            'tenant_id'  => $tenant->id,
            'title'      => 'Kapitalallokation folgt risikoadjustierter Rendite',
            'statement'  => 'Reinvestitionen und Akquisitionen erfordern die Übertreffung der internen Mindestrendite bei striktem Schutz der Holding-Liquidität.',
            'rationale'  => 'Substanzsicherung durch Mindestliquidität von 6 Monaten bei gleichzeitiger Renditemaximierung freier Cashflows.',
            'state'      => Active::class,
            'version'    => 1,
            'created_by' => $owner->id,
        ]);
        $kaPrinciple->linkTo(
            $kaModule,
            RelationType::Governs,
            ['scope' => 'Holding-Treasury, M&A-Investitionen und Dividendenpolitik']
        );

        Goal::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $kaModule->id,
            'title'        => 'Zielzustand Optimale Kapitalproduktivität',
            'description'  => 'Disziplinierte Kapitalallokation nach ROCE-Schwellenwerten (>= 18%) und Wahrung einer Liquiditätsreserve von mind. 6 Monaten operativen Kosten.',
            'target_score' => 95.00,
            'version'      => 1,
            'created_by'   => $owner->id,
        ]);

        $kaAuditTemplate = AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $kaModule->id,
            'name'        => 'Treasury- & Kapitalallokations-Audit',
            'description' => 'Evaluierung von Liquiditätsabsicherung, Hurdle-Rate-Konformität und Reinvestitionsdisziplin.',
        ]);

        $kaQ1 = AuditQuestion::create([
            'audit_template_id' => $kaAuditTemplate->id,
            'question_text'     => 'Wird jede Investitionsentscheidung an einem definierten Hurdle-Rate-Modell (>= 18% ROCE) gemessen?',
            'guidance'          => 'Dokumentierte DCF- und ROI-Kalkulationen vor Investitionsfreigabe.',
            'weight'            => 20,
            'max_score'         => 5,
            'order'             => 1,
        ]);

        $kaQ2 = AuditQuestion::create([
            'audit_template_id' => $kaAuditTemplate->id,
            'question_text'     => 'Ist eine freie Liquiditätsreserve von mindestens 6 Monaten operativen Kosten garantiert?',
            'guidance'          => 'Bankguthaben und zugesagte freie Kreditlinien im Treasury-Report.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 2,
        ]);

        $kaQ3 = AuditQuestion::create([
            'audit_template_id' => $kaAuditTemplate->id,
            'question_text'     => 'Erfolgt eine quartalsweise Reallokationsprüfung nicht gebundener Überschussliquidität?',
            'guidance'          => 'Protokoll des Allokationskomitees der Geschäftsführung.',
            'weight'            => 15,
            'max_score'         => 5,
            'order'             => 3,
        ]);

        $kaRun = app(RunAudit::class)->handle(
            module: $kaModule,
            template: $kaAuditTemplate,
            auditor: $steward
        );

        app(ScoreAudit::class)->handle(
            run: $kaRun,
            responses: [
                $kaQ1->id => ['score' => 5, 'notes' => 'Hurdle-Rate-Prüfung bei allen Investitionen strikt angewandt.'],
                $kaQ2->id => ['score' => 5, 'notes' => 'Liquiditätsreserve liegt aktuell bei 7.4 Monaten.'],
                $kaQ3->id => ['score' => 4, 'notes' => 'Reallokation im Quartalsrhythmus durchgeführt.'],
            ]
        );

        $kaKpi = Kpi::create([
            'tenant_id'    => $tenant->id,
            'module_id'    => $kaModule->id,
            'name'         => 'Return on Capital Employed (ROCE)',
            'code'         => 'KPI_ROCE_ALLOCATION',
            'unit'         => '%',
            'direction'    => 'higher_is_better',
            'target_value' => 18.0,
        ]);

        app(RecordKpiReading::class)->handle(
            kpi: $kaKpi,
            value: 19.2,
            recorder: $steward,
            notes: 'Jahresergebnisberechnung Holding 2025/2026'
        );

        $kaTool = Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $kaModule->id,
            'name'        => 'Kapitalallokations- & Hurdle-Rate-Rechner',
            'type'        => Tool::TYPE_SAAS,
            'url_or_path' => '/tools/hurdle_rate_calculator.xlsx',
            'description' => 'DCF- und Hurdle-Rate-Kalkulationstool zur Bewertung von Investitions- und Reinvestitionsoptionen.',
        ]);

        $kaTool->linkTo(
            $kaModule,
            RelationType::Improves,
            ['impact' => 'Sichert disziplinierte Investitionsentscheidungen über dem Kapitalkostensatz']
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

        // Seed Canonical ALLOCORE MASTER AUDIT (AMAR) Template
        app(\App\Domains\Development\Services\AmarTemplateService::class)
            ->ensureAmarTemplate($tenant, $createdModules['unternehmensentwicklung'] ?? null);
    }
}
