<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Development\Actions\RecordKpiReading;
use App\Domains\Development\Actions\RunAudit;
use App\Domains\Development\Actions\ScoreAudit;
use App\Domains\Development\Models\AuditQuestion;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Review\Actions\CloseReview;
use App\Domains\Review\Models\Review;
use App\Domains\Review\Models\ReviewItem;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Membership;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DashboardActionController extends Controller
{
    /**
     * 1. Quick Capture: Frictionless logging of Observations, Learnings, or Questions.
     */
    public function capture(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'    => 'required|in:observation,learning,question',
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = $request->user();
        $tenant = TenantContext::getTenant() ?? Tenant::first();

        match ($validated['type']) {
            'observation' => Observation::create([
                'tenant_id'  => $tenant->id,
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'created_by' => $user->id,
            ]),
            'learning' => Learning::create([
                'tenant_id'  => $tenant->id,
                'title'      => $validated['title'],
                'summary'    => $validated['content'],
                'state'      => Draft::class,
                'created_by' => $user->id,
            ]),
            'question' => Question::create([
                'tenant_id'  => $tenant->id,
                'title'      => $validated['title'],
                'context'    => $validated['content'],
                'status'     => Question::STATUS_OPEN,
                'created_by' => $user->id,
            ]),
        };

        $label = ucfirst($validated['type']);
        return redirect()->route('dashboard')->with('success', "✓ {$label} successfully captured and logged into DOS Knowledge Graph.");
    }

    /**
     * 2. Run & Score Audit: Allows user to evaluate questions and immediately update maturity score.
     */
    public function submitAudit(
        Request $request,
        RunAudit $runAction,
        ScoreAudit $scoreAction
    ): RedirectResponse {
        $validated = $request->validate([
            'module_id'         => 'required|exists:modules,id',
            'audit_template_id' => 'required|exists:audit_templates,id',
            'responses'         => 'required|array',
            'responses.*.score' => 'required|integer|min:1|max:5',
            'responses.*.notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $module = Module::withoutGlobalScopes()->findOrFail($validated['module_id']);
        $template = AuditTemplate::withoutGlobalScopes()->findOrFail($validated['audit_template_id']);

        $run = $runAction->handle($module, $template, $user);
        $scoredRun = $scoreAction->handle($run, $validated['responses']);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Audit completed for {$module->name}! Maturity score updated to {$scoredRun->overall_score}%."
        );
    }

    /**
     * 3. Record KPI Reading: Update metric value live.
     */
    public function recordKpi(Request $request, RecordKpiReading $action): RedirectResponse
    {
        $validated = $request->validate([
            'kpi_id' => 'required|exists:kpis,id',
            'value'  => 'required|numeric',
            'notes'  => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $kpi = Kpi::withoutGlobalScopes()->findOrFail($validated['kpi_id']);

        $action->handle(
            kpi: $kpi,
            value: (float) $validated['value'],
            recorder: $user,
            notes: $validated['notes'] ?? 'Manual executive update'
        );

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Recorded new metric reading for {$kpi->name}: {$validated['value']} {$kpi->unit}"
        );
    }

    /**
     * 4. Close Review: Closes review and automatically spawns draft Learning in ALF.
     */
    public function closeReview(
        Review $review,
        Request $request,
        CloseReview $action
    ): RedirectResponse {
        $user = $request->user();

        if ($review->status === Review::STATUS_CLOSED) {
            return redirect()->route('dashboard')->with(
                'info',
                "Review '{$review->title}' ist bereits abgeschlossen."
            );
        }

        $learningPayload = [
            'title'     => 'Learning from ' . $review->title,
            'summary'   => $review->summary ?? 'Key takeaways from closed review period ' . $review->period,
            'rationale' => 'Automated feedback loop closure from ARF review ' . $review->id,
        ];

        try {
            $action->handle($review, $user, $learningPayload);
        } catch (\LogicException $e) {
            return redirect()->route('dashboard')->with(
                'info',
                "Review '{$review->title}' ist bereits abgeschlossen."
            );
        }

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Review '{$review->title}' closed! A new Draft Learning was automatically spawned into ALF."
        );
    }

    /**
     * 5. Create Review: Create a new strategic review period with associated audit questions.
     */
    public function createReview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'                   => 'required|string|max:255',
            'period'                  => 'required|string|max:50',
            'review_date'             => 'required|date',
            'summary'                 => 'required|string',
            'questions'               => 'nullable|array',
            'questions.*'             => 'nullable|string|max:500',
            'question_modules'        => 'nullable|array',
            'existing_question_ids'   => 'nullable|array',
            'existing_question_ids.*' => 'nullable|integer|exists:audit_questions,id',
        ]);

        $tenant = TenantContext::getTenant() ?? Tenant::first();

        $review = Review::create([
            'tenant_id'   => $tenant->id,
            'title'       => $validated['title'],
            'period'      => $validated['period'],
            'review_date' => $validated['review_date'],
            'summary'     => $validated['summary'],
            'status'      => Review::STATUS_OPEN,
            'created_by'  => $request->user()->id,
        ]);

        $itemsCount = 0;

        // 1. Process custom entered audit questions
        if (!empty($validated['questions'])) {
            foreach ($validated['questions'] as $index => $qText) {
                $qText = trim((string) $qText);
                if ($qText === '') {
                    continue;
                }

                $moduleId = !empty($validated['question_modules'][$index])
                    ? (int) $validated['question_modules'][$index]
                    : null;

                ReviewItem::create([
                    'review_id' => $review->id,
                    'module_id' => $moduleId,
                    'topic'     => $qText,
                    'status'    => 'identified',
                    'notes'     => 'Im Review eingereichte Audit-Frage',
                ]);
                $itemsCount++;
            }
        }

        // 2. Process selected existing audit questions
        if (!empty($validated['existing_question_ids'])) {
            $existingQuestions = AuditQuestion::with('template')
                ->whereIn('id', $validated['existing_question_ids'])
                ->get();

            foreach ($existingQuestions as $eq) {
                ReviewItem::create([
                    'review_id' => $review->id,
                    'module_id' => $eq->template?->module_id,
                    'topic'     => $eq->question_text,
                    'status'    => 'identified',
                    'notes'     => $eq->guidance ? "Guidance: {$eq->guidance}" : 'Bestehende Modul-Audit-Frage',
                ]);
                $itemsCount++;
            }
        }

        $itemsMsg = $itemsCount > 0 ? " mit {$itemsCount} zugeordneten Audit-Fragen" : '';

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Strategisches Review '{$validated['title']}' für Periode {$validated['period']}{$itemsMsg} erfolgreich angelegt."
        );
    }

    /**
     * 5. Add User / Team Member: Allows creating or adding a user to the current organization/tenant.
     */
    public function addUser(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant() ?? Tenant::first();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'role'     => 'required|string|in:owner,steward,contributor,observer',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name'     => $validated['name'],
                'password' => Hash::make($validated['password']),
            ]
        );

        if ($tenant) {
            Membership::updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $user->id],
                ['role' => $validated['role']]
            );
        }

        $roleLabel = ucfirst($validated['role']);
        return redirect()->route('dashboard')->with(
            'success',
            "✓ User '{$user->name}' ({$user->email}) added successfully with role '{$roleLabel}' to {$tenant->name}."
        );
    }

    /**
     * 6. Create / Develop New Module: Propose or develop a new operational module with initial Zielzustand based on AMF 1.1.
     */
    public function createModule(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant() ?? $request->user()?->tenants()->first() ?? Tenant::first();

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'slug'                 => 'nullable|string|max:255',
            'description'          => 'required|string',
            'parent_id'            => 'nullable|exists:modules,id',
            'target_title'         => 'nullable|string|max:255',
            'target_score'         => 'nullable|numeric|min:1|max:100',
            'development_object'   => 'nullable|string|max:255',
            'allocore_level'       => 'nullable|string|max:255',
            'amf_version'          => 'nullable|string|max:50',
            'audit_question'       => 'nullable|string|max:500',
            'audit_guidance'       => 'nullable|string|max:500',
            'kpi_name'             => 'nullable|string|max:255',
            'kpi_target'           => 'nullable|numeric',
            'kpi_unit'             => 'nullable|string|max:50',
            'tool_name'            => 'nullable|string|max:255',
            'tool_type'            => 'nullable|string|in:template,checklist,whitepaper,saas,ai_function',
            'learning_hypothesis'  => 'nullable|string|max:500',
            'review_cadence'       => 'nullable|string|max:50',
            'review_focus'         => 'nullable|string|max:500',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Module::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $maxOrder = Module::max('order') ?? 0;

        $description = $validated['description'];
        if (!empty($validated['development_object']) || !empty($validated['allocore_level'])) {
            $prefixes = [];
            if (!empty($validated['development_object'])) {
                $prefixes[] = "Objekt: {$validated['development_object']}";
            }
            if (!empty($validated['allocore_level'])) {
                $prefixes[] = "Ebene: {$validated['allocore_level']}";
            }
            $description = '[' . implode(' | ', $prefixes) . '] ' . $description;
        }

        $module = Module::create([
            'tenant_id'   => $tenant->id,
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $description,
            'parent_id'   => $validated['parent_id'] ?? null,
            'order'       => $maxOrder + 1,
        ]);

        $versionInt = 1;
        if (!empty($validated['amf_version'])) {
            if (str_contains($validated['amf_version'], '0.1')) {
                $versionInt = 1;
            } elseif (str_contains($validated['amf_version'], '0.2')) {
                $versionInt = 2;
            } elseif (str_contains($validated['amf_version'], '1.0')) {
                $versionInt = 3;
            } elseif (str_contains($validated['amf_version'], '2.0')) {
                $versionInt = 4;
            }
        }

        $createdComponents = [];

        // Baustein 2: Zieldefinition (Goal)
        if (!empty($validated['target_title']) || !empty($validated['target_score'])) {
            $targetTitle = $validated['target_title'] ?: "Zielzustand für {$module->name}";
            $objText = !empty($validated['development_object']) ? " ({$validated['development_object']})" : '';
            $goalDesc = "Messbare AMF 1.1 Zieldefinition für {$module->name}{$objText}: {$targetTitle}";

            Goal::create([
                'tenant_id'    => $tenant->id,
                'module_id'    => $module->id,
                'title'        => $targetTitle,
                'description'  => $goalDesc,
                'target_score' => $validated['target_score'] ?: 85.0,
                'version'      => $versionInt,
                'created_by'   => $request->user()->id,
            ]);
            $createdComponents[] = 'Zieldefinition';
        }

        // Baustein 4: Strukturiertes Audit (AuditTemplate & AuditQuestion)
        if (!empty($validated['audit_question'])) {
            $template = AuditTemplate::create([
                'tenant_id'   => $tenant->id,
                'module_id'   => $module->id,
                'name'        => "Audit {$module->name}",
                'description' => "AMF 1.1 Reifegrad-Audit für {$module->name}",
            ]);

            AuditQuestion::create([
                'tenant_id'         => $tenant->id,
                'audit_template_id' => $template->id,
                'question_text'     => $validated['audit_question'],
                'weight'            => 1.0,
                'order'             => 1,
                'guidance'          => $validated['audit_guidance'] ?: 'Reifegrad-Messung (1=Initiale Phase bis 5=Vollständig institutionalisiert).',
            ]);
            $createdComponents[] = 'Audit-Fragebogen';
        }

        // Baustein 5: KPI-System (Kpi)
        if (!empty($validated['kpi_name'])) {
            Kpi::create([
                'tenant_id'    => $tenant->id,
                'module_id'    => $module->id,
                'name'         => $validated['kpi_name'],
                'code'         => strtoupper(Str::slug($module->name . '-' . $validated['kpi_name'])),
                'unit'         => $validated['kpi_unit'] ?: '%',
                'direction'    => 'asc',
                'target_value' => $validated['kpi_target'] ?? (float) ($validated['target_score'] ?? 85.0),
            ]);
            $createdComponents[] = 'KPI-Messregel';
        }

        // Baustein 6: Werkzeuge / Tools (Tool)
        if (!empty($validated['tool_name'])) {
            Tool::create([
                'tenant_id'   => $tenant->id,
                'module_id'   => $module->id,
                'name'        => $validated['tool_name'],
                'type'        => $validated['tool_type'] ?: Tool::TYPE_TEMPLATE,
                'url_or_path' => '#',
                'description' => "AMF 1.1 Befähigungswerkzeug für {$module->name}",
            ]);
            $createdComponents[] = 'Befähigungswerkzeug';
        }

        // Baustein 7: Learnings / ALF (Observation)
        if (!empty($validated['learning_hypothesis'])) {
            Observation::create([
                'tenant_id'   => $tenant->id,
                'title'       => "Ausgangsbeobachtung: {$module->name}",
                'content'     => $validated['learning_hypothesis'],
                'context'     => ['module' => $module->slug, 'framework' => 'AMF 1.1'],
                'created_by'  => $request->user()->id,
            ]);
            $createdComponents[] = 'ALF-Ausgangsbeobachtung';
        }

        // Baustein 8: Review-Zyklus / ARF (Review & ReviewItem)
        if (!empty($validated['review_focus']) || !empty($validated['review_cadence'])) {
            $cadence = $validated['review_cadence'] ?: 'Q-Review';
            $period = date('Y') . '-' . $cadence;
            $focus = $validated['review_focus'] ?: "Initiales AMF 1.1 Review für {$module->name}";

            $review = Review::create([
                'tenant_id'   => $tenant->id,
                'title'       => "Review: {$module->name} ({$cadence})",
                'period'      => $period,
                'review_date' => now()->addMonths(3)->toDateString(),
                'summary'     => $focus,
                'status'      => Review::STATUS_OPEN,
                'created_by'  => $request->user()->id,
            ]);

            ReviewItem::create([
                'review_id' => $review->id,
                'module_id' => $module->id,
                'topic'     => $validated['audit_question'] ?: "Überprüfung Zielerreichung: {$validated['name']}",
                'status'    => 'identified',
                'notes'     => "AMF 1.1 Initialer Review-Schwerpunkt ({$cadence})",
            ]);
            $createdComponents[] = 'ARF-Reviewzyklus';
        }

        $objFeedback = !empty($validated['development_object']) ? " (Entwicklungsobjekt: {$validated['development_object']})" : '';
        $componentsMsg = !empty($createdComponents) ? ' inklusive: ' . implode(', ', $createdComponents) : '';

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Neues Modul '{$module->name}'{$objFeedback} nach AMF 1.1 Blaupause angelegt{$componentsMsg}."
        );
    }
}
