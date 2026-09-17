<?php

declare(strict_types=1);

namespace App\Domains\Development\Services;

use App\Domains\Development\Models\AuditQuestion;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Module;
use App\Domains\Tenancy\Models\Tenant;

class AmarTemplateService
{
    public const TEMPLATE_NAME = 'ALLOCORE MASTER AUDIT (AMAR)';
    public const TEMPLATE_VERSION = '1.0';

    /**
     * Get the standardized 10-Area definition for the Allocore Master Audit.
     *
     * @return array<string, array{goal: string, questions: array<int, array{text: string, guidance: string, weight: int, type: string, section?: string}>}>
     */
    public static function getDefinition(): array
    {
        return [
            'AREA 1: ONBOARDING' => [
                'goal' => 'Der Nutzer versteht sofort: Was Allocore ist, warum er hier ist und welchen Nutzen er hat.',
                'questions' => [
                    ['text' => 'Value Proposition verständlich (Value Proposition understandable)', 'guidance' => '1=Unklar, 5=Kristallklarer Nutzen in 10 Sekunden', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Zielgruppe erkennt sich wieder (Target group recognizes itself)', 'guidance' => '1=Zu allgemein, 5=Direkte Identifikation als Unternehmer', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Relevanz sofort erkennbar (Relevance immediately recognizable)', 'guidance' => '1=Unwichtig, 5=Unmittelbare Dringlichkeit spürbar', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Sprache verständlich (Language understandable)', 'guidance' => '1=Jargon-überladen, 5=Präzise, klare Unternehmersprache', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Call to Action klar (Call to action clear)', 'guidance' => '1=Keine Handlungsaufforderung, 5=Nächster Klick unmissverständlich', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Erkenntnisfrage: Würde ein Unternehmer nach 60 Sekunden weiterlesen?', 'guidance' => 'Schlüsseltest zur Onboarding-Fesselung (JA / NEIN)', 'weight' => 5, 'type' => 'binary'],
                ],
            ],
            'AREA 2: AUDIT' => [
                'goal' => 'Der Nutzer beantwortet die Fragen gerne und versteht ihren Sinn.',
                'questions' => [
                    ['text' => 'Verständlich (Understandable)', 'guidance' => 'Fragen sind eindeutig formuliert ohne Fehlinterpretationen', 'weight' => 4, 'type' => 'rating', 'section' => 'Kriterien pro Frage'],
                    ['text' => 'Klar relevant (Clearly relevant)', 'guidance' => 'Jede Frage trifft einen substantiellen Unternehmensnerv', 'weight' => 5, 'type' => 'rating', 'section' => 'Kriterien pro Frage'],
                    ['text' => 'Authentisch (Authentic)', 'guidance' => 'Fragen spiegeln reale unternehmerische Herausforderungen wider', 'weight' => 4, 'type' => 'rating', 'section' => 'Kriterien pro Frage'],
                    ['text' => 'Liefert echten Erkenntnisgewinn (Delivers real gain in knowledge)', 'guidance' => 'Schon das Nachdenken über die Frage stiftet unmittelbaren Mehrwert', 'weight' => 5, 'type' => 'rating', 'section' => 'Kriterien pro Frage'],
                    ['text' => 'Fragen wirken sinnvoll (Questions work meaningfully)', 'guidance' => 'Logischer Aufbau und sinnvolle Fragereihenfolge', 'weight' => 4, 'type' => 'rating', 'section' => 'Gesamteindruck'],
                    ['text' => 'Fragen haben praktische Wirkung (Questions have a practical effect)', 'guidance' => 'Direkte Anwendbarkeit auf die Betriebspraxis', 'weight' => 5, 'type' => 'rating', 'section' => 'Gesamteindruck'],
                    ['text' => 'Fragen schaffen Vertrauen (Questions create trust)', 'guidance' => 'Vermittelt hohe fachliche und strategische Kompetenz', 'weight' => 5, 'type' => 'rating', 'section' => 'Gesamteindruck'],
                    ['text' => 'Audit macht Freude (Audit is fun)', 'guidance' => 'Geringe kognitive Ermüdung, flüssiger Flow', 'weight' => 3, 'type' => 'rating', 'section' => 'Gesamteindruck'],
                    ['text' => 'Nutzer bleibt motiviert (User stays motivated)', 'guidance' => 'Hohe Abschlussquote ohne Abbrüche', 'weight' => 4, 'type' => 'rating', 'section' => 'Gesamteindruck'],
                ],
            ],
            'AREA 3: EVALUATION' => [
                'goal' => 'Der Nutzer denkt: „Das beschreibt mein Unternehmen überraschend treffend.“',
                'questions' => [
                    ['text' => 'Ergebnisse nachvollziehbar (Results comprehensible)', 'guidance' => 'Auswertung ist logisch und transparent hergeleitet', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Ergebnisse glaubwürdig (Results credible)', 'guidance' => 'Keine generischen Allgemeinplätze, sondern reale Diagnose', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Stärken korrekt identifiziert (Strengths correctly identified)', 'guidance' => 'Treffende Erfassung bestehender Wettbewerbsvorteile', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Nächster Entwicklungsengpass korrekt erkannt (Next development bottleneck correctly identified)', 'guidance' => 'Identifiziert den wahren limitierenden Faktor (TOC)', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Erklärung verständlich (Explanation understandable)', 'guidance' => 'Klar formuliert ohne Berater-Kauderwelsch', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Schlüsseltest: Kann ein Nutzer erklären: Wo er steht? Warum? Was als Nächstes kommt?', 'guidance' => 'Kernprüfung der Auswertungsqualität (JA / NEIN)', 'weight' => 5, 'type' => 'binary'],
                ],
            ],
            'AREA 4: AI COACH' => [
                'goal' => 'Der Nutzer erhält Orientierung und klare Handlungsempfehlungen.',
                'questions' => [
                    ['text' => 'Passend zum Audit (Suitable for the audit)', 'guidance' => 'Direkte Bezugnahme auf die erkannten Audit-Engpässe', 'weight' => 5, 'type' => 'rating', 'section' => 'Pro Empfehlung'],
                    ['text' => 'Verständlich formuliert (Understandable)', 'guidance' => 'Prägnante, klare Sprache ohne Missverständnisse', 'weight' => 4, 'type' => 'rating', 'section' => 'Pro Empfehlung'],
                    ['text' => 'Konkret umsetzbar (Concrete actionable)', 'guidance' => 'Keine Theorie, sondern sofort ausführbare Schritte', 'weight' => 5, 'type' => 'rating', 'section' => 'Pro Empfehlung'],
                    ['text' => 'Motivierend gestaltet (Motivating)', 'guidance' => 'Aktivierender Tonus statt entmutigender Defizit-Fokus', 'weight' => 4, 'type' => 'rating', 'section' => 'Pro Empfehlung'],
                    ['text' => 'Empfehlungen wirken individuell (Recommendations work individually)', 'guidance' => 'Spürbar maßgeschneidert auf den Einzelfall', 'weight' => 4, 'type' => 'rating', 'section' => 'Gesamteindruck Coach'],
                    ['text' => 'Empfehlungen wirken hilfreich (Recommendations work helpful)', 'guidance' => 'Spürbare Entlastung des Unternehmers', 'weight' => 5, 'type' => 'rating', 'section' => 'Gesamteindruck Coach'],
                    ['text' => 'Empfehlungen schaffen Motivation (Recommendations create motivation)', 'guidance' => 'Impuls zur unmittelbaren Umsetzung', 'weight' => 4, 'type' => 'rating', 'section' => 'Gesamteindruck Coach'],
                    ['text' => 'Nutzer versteht den nächsten Schritt (User understands next step)', 'guidance' => 'Klarheit über die unmittelbare Priorität', 'weight' => 5, 'type' => 'rating', 'section' => 'Gesamteindruck Coach'],
                ],
            ],
            'AREA 5: BOOKS' => [
                'goal' => 'Für jede Buchempfehlung: Passend, fundiert und überzeugend begründet.',
                'questions' => [
                    ['text' => 'Passend zur Kategorie (Suitable for the category)', 'guidance' => 'Buch adressiert das exakte Fachgebiet', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Passend zum Entwicklungsstand (Suitable for the state of development)', 'guidance' => 'Überfordert nicht und unterfordert nicht', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Klar begründet (Clearly justified)', 'guidance' => 'Warum dieses Werk und kein anderes', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Erwarteter Nutzen beschrieben (Expected benefit described)', 'guidance' => 'Konkrete Erkenntnis vorab skizziert', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Einfach zu verstehen (Easy to understand)', 'guidance' => 'Niedrige Leseschwelle, hohe Praxisrelevanz', 'weight' => 3, 'type' => 'rating'],
                    ['text' => 'Kritische Frage: Versteht der Nutzer: Warum genau dieses Buch?', 'guidance' => 'Plausibilitätsprüfung der Literaturempfehlung (JA / NEIN)', 'weight' => 4, 'type' => 'binary'],
                ],
            ],
            'AREA 6: TOOL RECOMMENDATIONS' => [
                'goal' => 'Für jede Tool-Empfehlung: Relevanter Problembezug und spürbarer Nutzen.',
                'questions' => [
                    ['text' => 'Problembezug erkennbar (Problem Reference Recognizable)', 'guidance' => 'Löst eine konkrete operative Reibung', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Kategoriebezug erkennbar (Category Reference Recognizable)', 'guidance' => 'Passt nahtlos in den Modulbereich', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Nutzen verständlich (Benefits Understandable)', 'guidance' => 'Wertversprechen des Werkzeugs ist sofort klar', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Erwartetes Ergebnis beschrieben (Expected Result Described)', 'guidance' => 'Was liegt nach Tool-Einsatz fertig vor?', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Kritische Frage: Versteht der Nutzer: Warum wurde mir genau dieses Tool empfohlen?', 'guidance' => 'Plausibilitätsprüfung der Werkzeugauswahl (JA / NEIN)', 'weight' => 4, 'type' => 'binary'],
                ],
            ],
            'AREA 7: TOOL AUDIT' => [
                'goal' => 'Für jedes einzelne Werkzeug: Funktion, Usability, Nutzen und Regelkreis.',
                'questions' => [
                    ['text' => 'Funktion: Fehlerfrei (Error-free)', 'guidance' => 'Keine Bugs, fehlerfreie Darstellung und Logik', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 1: Funktion'],
                    ['text' => 'Funktion: Schnell (Fast)', 'guidance' => 'Keine spürbaren Ladezeiten oder Verzögerungen', 'weight' => 4, 'type' => 'rating', 'section' => 'Teil 1: Funktion'],
                    ['text' => 'Funktion: Stabil (Stable)', 'guidance' => 'Zuverlässig auch bei häufiger Nutzung', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 1: Funktion'],
                    ['text' => 'Funktion: Mobil nutzbar (Mobile Usable)', 'guidance' => 'Volle Funktion auch auf Smartphones/Tablets', 'weight' => 4, 'type' => 'rating', 'section' => 'Teil 1: Funktion'],
                    ['text' => 'Usability: Zweck sofort verständlich (Purpose immediately understandable)', 'guidance' => 'Keine Einlesezeit erforderlich', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 2: Usability'],
                    ['text' => 'Usability: Bedienung intuitiv (Operation intuitive)', 'guidance' => 'Selbsterklärende Steuerelemente', 'weight' => 4, 'type' => 'rating', 'section' => 'Teil 2: Usability'],
                    ['text' => 'Usability: Ergebnisse verständlich (Results understandable)', 'guidance' => 'Tool-Output ist sofort verwertbar', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 2: Usability'],
                    ['text' => 'Usability: Nutzerführung klar (User guidance clear)', 'guidance' => 'Roter Faden durch die Tool-Nutzung', 'weight' => 4, 'type' => 'rating', 'section' => 'Teil 2: Usability'],
                    ['text' => 'Nutzen: Spart Zeit (Saves Time)', 'guidance' => 'Erhebliche Arbeitszeitersparnis gegenüber manueller Arbeit', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 3: Nutzen'],
                    ['text' => 'Nutzen: Verbessert Entscheidungen (Improves Decisions)', 'guidance' => 'Erhöht die Entscheidungsqualität messbar', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 3: Nutzen'],
                    ['text' => 'Nutzen: Unterstützt Umsetzung (Supports Implementation)', 'guidance' => 'Befähigt das Team zur eigenständigen Durchführung', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 3: Nutzen'],
                    ['text' => 'Nutzen: Verbessert Audit-Kategorie (Improves Audit Category)', 'guidance' => 'Führt direkt zu besseren Reifegradwerten', 'weight' => 4, 'type' => 'rating', 'section' => 'Teil 3: Nutzen'],
                    ['text' => 'Regelkreis: Unterstützt nächste Entwicklungsstufe (Supports Next Level of Development)', 'guidance' => 'Ermöglicht den Übergang zum nächsten Reifegrad', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 4: Regelkreis'],
                    ['text' => 'Regelkreis: Erzeugt messbaren Fortschritt (Generates Measurable Progress)', 'guidance' => 'Schlägt sich in KPIs nieder', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 4: Regelkreis'],
                    ['text' => 'Regelkreis: Ermutigt zur Wiederverwendung (Encourages Reuse)', 'guidance' => 'Wird zum festen Bestandteil der Unternehmensorganisation', 'weight' => 4, 'type' => 'rating', 'section' => 'Teil 4: Regelkreis'],
                    ['text' => 'Regelkreis: Schließt Regelkreis (Closes Control Loop)', 'guidance' => 'Rückkopplung ins ARF/ALF gewährleistet', 'weight' => 5, 'type' => 'rating', 'section' => 'Teil 4: Regelkreis'],
                ],
            ],
            'AREA 8: IMPLEMENTATION' => [
                'goal' => 'Der Nutzer wird aktiv: Niedrige Hürden, hohe Umsetzungsquote.',
                'questions' => [
                    ['text' => 'Umsetzung wird erleichtert (Implementation is facilitated)', 'guidance' => 'Schritt-für-Schritt Anleitung senkt Barrieren', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Hürden sind niedrig (Hurdles are low)', 'guidance' => 'Keine technischen oder bürokratischen Blockaden', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Fortschritt sichtbar (Progress visible)', 'guidance' => 'Erfolge werden im System transparent abgebildet', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Nutzer bleibt motiviert (User stays motivated)', 'guidance' => 'Durchhaltevermögen bis zum Projektabschluss', 'weight' => 4, 'type' => 'rating'],
                ],
            ],
            'AREA 9: PROGRESS' => [
                'goal' => 'Der Nutzer erkennt Entwicklung: Vorher/Nachher messbar und sichtbar.',
                'questions' => [
                    ['text' => 'Vorher & Nachher sichtbar (Before & After Visible)', 'guidance' => 'Vergleich zum Ausgangszustand ist optisch klar', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Verbesserungen messbar (Improvements Measurable)', 'guidance' => 'KPIs und Audit-Scores belegen den Fortschritt', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Erfolge sichtbar (Successes Visible)', 'guidance' => 'Teamerfolge werden greifbar gefeiert', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Entwicklung motiviert (Development Motivates)', 'guidance' => 'Erfolg beflügelt die nächste Entwicklungsphase', 'weight' => 4, 'type' => 'rating'],
                    ['text' => 'Schlüsseltest: Kann ein Nutzer erkennen: Allocore hilft mir wirklich?', 'guidance' => 'Beweis echter unternehmerischer Transformation (JA / NEIN)', 'weight' => 5, 'type' => 'binary'],
                ],
            ],
            'AREA 10: ENTHUSIASM' => [
                'goal' => 'Mehrwert größer als Zeitaufwand: Wahre Begeisterung und Weiterempfehlung.',
                'questions' => [
                    ['text' => 'Zeitaufwand gerechtfertigt (Time Expenditure Justified)', 'guidance' => 'Nutzen übertrifft investierte Stunden um ein Vielfaches', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Nutzer erlebt Aha-Momente (User Experiences Aha Moments)', 'guidance' => 'Tiefgreifende strategische Erleuchtungen', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Nutzer würde wiederkommen (User Would Come Back)', 'guidance' => 'Hohe Wiederkehrrate und Bindung', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Nutzer würde weiterempfehlen (User Would Recommend)', 'guidance' => 'Net Promoter Score / Weiterempfehlungsbereitschaft', 'weight' => 5, 'type' => 'rating'],
                    ['text' => 'Nutzer spürt echten Mehrwert (User feels real added value)', 'guidance' => 'Unternehmenswert und Souveränität nachhaltig gestärkt', 'weight' => 5, 'type' => 'rating'],
                ],
            ],
        ];
    }

    /**
     * Ensure the canonical AMAR template exists for the given tenant and module.
     */
    public function ensureAmarTemplate(Tenant $tenant, ?Module $module = null): AuditTemplate
    {
        $module = $module ?? Module::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('slug', 'unternehmensentwicklung')
            ->first() ?? Module::withoutGlobalScopes()->where('tenant_id', $tenant->id)->first();

        if (!$module) {
            $module = Module::create([
                'tenant_id'   => $tenant->id,
                'name'        => 'Unternehmensentwicklung',
                'slug'        => 'unternehmensentwicklung',
                'description' => 'Strategische Ausrichtung, Organisation und Skalierungsstrukturen.',
                'order'       => 1,
            ]);
        }

        $template = AuditTemplate::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('name', self::TEMPLATE_NAME)
            ->first();

        if (!$template) {
            $template = AuditTemplate::create([
                'tenant_id'   => $tenant->id,
                'module_id'   => $module->id,
                'name'        => self::TEMPLATE_NAME,
                'description' => 'Standardisiertes Master-Audit zur regelmäßigen Qualitätsprüfung aller 10 Allocore-Bereiche inklusive Unternehmer-Test, Insight-Protokoll und kybernetischem Regelkreis.',
            ]);
        } else {
            $template->update([
                'module_id'   => $module->id,
                'description' => 'Standardisiertes Master-Audit zur regelmäßigen Qualitätsprüfung aller 10 Allocore-Bereiche inklusive Unternehmer-Test, Insight-Protokoll und kybernetischem Regelkreis.',
            ]);
        }

        $definition = self::getDefinition();
        $order = 1;

        foreach ($definition as $areaName => $areaData) {
            foreach ($areaData['questions'] as $qData) {
                AuditQuestion::updateOrCreate(
                    [
                        'audit_template_id' => $template->id,
                        'question_text'     => $qData['text'],
                    ],
                    [
                        'area'           => $areaName,
                        'type'           => $qData['type'],
                        'section_header' => $qData['section'] ?? null,
                        'guidance'       => $qData['guidance'],
                        'weight'         => $qData['weight'],
                        'max_score'      => $qData['type'] === 'binary' ? 1 : 5,
                        'order'          => $order++,
                    ]
                );
            }
        }

        return $template->fresh(['questions']);
    }
}
