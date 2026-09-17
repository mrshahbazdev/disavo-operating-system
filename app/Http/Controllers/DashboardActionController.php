<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Development\Actions\RecordKpiReading;
use App\Domains\Development\Actions\RunAudit;
use App\Domains\Development\Actions\ScoreAudit;
use App\Domains\Development\Models\AuditQuestion;
use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Actions\PromoteLearningToPrinciple;
use App\Domains\Knowledge\Actions\ValidateLearning;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Knowledge\States\Learning\Validated;
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
            'audit_run_id'               => 'nullable|exists:audit_runs,id',
            'module_id'                  => 'required|exists:modules,id',
            'audit_template_id'          => 'required|exists:audit_templates,id',
            'version'                    => 'nullable|string|max:50',
            'user_paths_audited'         => 'nullable|integer|min:0',
            'tools_audited'              => 'nullable|integer|min:0',
            'previous_score'             => 'nullable|numeric|min:0|max:100',
            'entrepreneur_test_passed'   => 'nullable',
            'new_feature_ban'            => 'nullable',
            'responses'                  => 'required|array',
            'responses.*.score'          => 'nullable|integer|min:0|max:5',
            'responses.*.binary_answer'  => 'nullable',
            'responses.*.status_symbol'  => 'nullable|string|max:50',
            'responses.*.notes'          => 'nullable|string',
            'insight_protocols'          => 'nullable|array',
            'cybernetic_feedback'        => 'nullable|array',
            'quotas'                     => 'nullable|array',
        ]);

        $user = $request->user();
        $module = Module::withoutGlobalScopes()->findOrFail($validated['module_id']);
        $template = AuditTemplate::withoutGlobalScopes()->findOrFail($validated['audit_template_id']);

        if (!empty($validated['audit_run_id'])) {
            $run = AuditRun::withoutGlobalScopes()->findOrFail($validated['audit_run_id']);
            $run->update([
                'conducted_by' => $user->id,
                'status'       => AuditRun::STATUS_IN_PROGRESS,
            ]);
        } else {
            $run = $runAction->handle($module, $template, $user);
        }

        // Cybernetic gate check:
        // "Is Allocore more valuable to an entrepreneur today than it was before the last audit? YES / NO"
        // If answered NO, trigger New Feature Ban!
        $cyberFeedback = $validated['cybernetic_feedback'] ?? [];
        $moreValuable = $cyberFeedback['more_valuable_today'] ?? null;
        $featureBanTriggered = false;

        if ($moreValuable !== null) {
            $val = is_string($moreValuable) ? strtolower(trim($moreValuable)) : $moreValuable;
            if ($val === 'no' || $val === 'nein' || $val === false || $val === '0' || $val === 0) {
                $featureBanTriggered = true;
            }
        }

        $newFeatureBan = !empty($validated['new_feature_ban']) || $featureBanTriggered;

        $extraMeta = [
            'version'                  => $validated['version'] ?? '1.0',
            'user_paths_audited'       => isset($validated['user_paths_audited']) ? (int) $validated['user_paths_audited'] : 1,
            'tools_audited'            => isset($validated['tools_audited']) ? (int) $validated['tools_audited'] : 0,
            'previous_score'           => isset($validated['previous_score']) && $validated['previous_score'] !== '' ? (float) $validated['previous_score'] : null,
            'entrepreneur_test_passed' => !empty($validated['entrepreneur_test_passed']),
            'new_feature_ban'          => $newFeatureBan,
            'insight_protocols'        => $validated['insight_protocols'] ?? [],
            'cybernetic_feedback'      => $cyberFeedback,
            'quotas'                   => $validated['quotas'] ?? [],
        ];

        $scoredRun = $scoreAction->handle($run, $validated['responses'], $extraMeta);

        if ($newFeatureBan) {
            return redirect()->route('dashboard')->with(
                'error',
                "⚠️ Audit '{$template->name}' abgeschlossen ({$scoredRun->overall_score}%). DA DAS SYSTEM NICHT NACHWEISBAR WERTVOLLER GEWORDEN IST, WURDE DER 'NEW FEATURE BAN' AKTIVIERT! Fokus muss auf Mängelbeseitigung liegen."
            );
        }

        $isAmar = str_contains(strtolower($template->name), 'amar') || str_contains(strtolower($template->name), 'master audit');
        $label = $isAmar ? 'ALLOCORE MASTER AUDIT (AMAR)' : "Audit '{$template->name}'";

        return redirect()->route('dashboard')->with(
            'success',
            "✓ {$label} erfolgreich abgeschlossen! Allocore Value Score: {$scoredRun->overall_score}%."
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

    /**
     * 7. Create Custom Audit Template (ARF Audit Designer)
     */
    public function createAuditTemplate(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant() ?? Tenant::first();

        $validated = $request->validate([
            'module_id'   => 'nullable|exists:modules,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions'   => 'required|array|min:1',
            'weights'     => 'nullable|array',
            'guidances'   => 'nullable|array',
        ]);

        $moduleId = $validated['module_id'] ?? Module::withoutGlobalScopes()->first()?->id;

        $template = AuditTemplate::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $moduleId,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? 'Benutzerdefiniertes Spezial-Audit',
        ]);

        $qCount = 0;
        foreach ($validated['questions'] as $idx => $item) {
            if (is_array($item)) {
                $qText = trim((string) ($item['question_text'] ?? ''));
                $weight = isset($item['weight']) ? (int) $item['weight'] : 3;
                $guidance = isset($item['guidance']) ? (string) $item['guidance'] : null;
            } else {
                $qText = trim((string) $item);
                $weight = !empty($validated['weights'][$idx]) ? (int) $validated['weights'][$idx] : 3;
                $guidance = !empty($validated['guidances'][$idx]) ? (string) $validated['guidances'][$idx] : null;
            }

            if ($qText === '') continue;

            AuditQuestion::create([
                'tenant_id'         => $tenant->id,
                'audit_template_id' => $template->id,
                'question_text'     => $qText,
                'weight'            => max(1, $weight),
                'order'             => $idx + 1,
                'guidance'          => $guidance,
            ]);
            $qCount++;
        }

        $moduleName = $template->module ? $template->module->name : 'Holding';
        return redirect()->route('dashboard')->with(
            'success',
            "✓ Spezial-Audit '{$template->name}' mit {$qCount} Kernfragen für '{$moduleName}' erfolgreich im ARF designt!"
        );
    }

    /**
     * 8. Schedule & Assign Audit Run (ARF Scheduler)
     */
    public function scheduleAuditRun(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant() ?? Tenant::first();

        if (!$request->has('conducted_by') && $request->has('auditor_id')) {
            $request->merge(['conducted_by' => $request->input('auditor_id')]);
        }

        $validated = $request->validate([
            'module_id'         => 'required|exists:modules,id',
            'audit_template_id' => 'required|exists:audit_templates,id',
            'title'             => 'nullable|string|max:255',
            'conducted_by'      => 'required|exists:users,id',
            'due_date'          => 'nullable|date',
            'cadence'           => 'nullable|string|max:50',
            'notes'             => 'nullable|string',
        ]);

        $template = AuditTemplate::withoutGlobalScopes()->findOrFail($validated['audit_template_id']);
        $module = Module::withoutGlobalScopes()->findOrFail($validated['module_id']);
        $auditor = User::findOrFail($validated['conducted_by']);

        $title = $validated['title'] ?: "{$template->name} ({$module->name})";

        AuditRun::create([
            'tenant_id'         => $tenant->id,
            'module_id'         => $module->id,
            'audit_template_id' => $template->id,
            'title'             => $title,
            'conducted_by'      => $auditor->id,
            'due_date'          => $validated['due_date'] ?? now()->addWeeks(2)->toDateString(),
            'cadence'           => $validated['cadence'] ?? 'Einmalig',
            'status'            => AuditRun::STATUS_SCHEDULED,
            'notes'             => $validated['notes'] ?? null,
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Audit '{$title}' erfolgreich terminiert und an {$auditor->name} zugewiesen!"
        );
    }

    /**
     * 9. Create / Design Tool for Module (AMF Tool Studio)
     */
    public function createTool(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant() ?? Tenant::first();

        $validated = $request->validate([
            'module_id'   => 'required|exists:modules,id',
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|in:checklist,template,whitepaper,saas,ai_function,sop,prompt,tool',
            'description' => 'nullable|string',
            'url_or_path' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
        ]);

        $module = Module::withoutGlobalScopes()->findOrFail($validated['module_id']);

        $tool = Tool::create([
            'tenant_id'   => $tenant->id,
            'module_id'   => $module->id,
            'name'        => $validated['name'],
            'type'        => $validated['type'],
            'description' => $validated['description'] ?? "Operatives Werkzeug für {$module->name}",
            'url_or_path' => $validated['url_or_path'] ?? '#',
            'content'     => $validated['content'],
            'is_active'   => true,
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Werkzeug '{$tool->name}' ({$tool->type}) erfolgreich für Modul '{$module->name}' im AMF-Studio angelegt!"
        );
    }

    /**
     * 10. ALF Step 1: Synthesize an Observation into a Draft Learning
     */
    public function synthesizeLearning(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant() ?? Tenant::first();

        $validated = $request->validate([
            'observation_id' => 'nullable|exists:observations,id',
            'title'          => 'required|string|max:255',
            'summary'        => 'required|string',
            'rationale'      => 'nullable|string',
        ]);

        $user = $request->user();

        $learning = Learning::create([
            'tenant_id'  => $tenant->id,
            'title'      => $validated['title'],
            'summary'    => $validated['summary'],
            'rationale'  => $validated['rationale'] ?? 'Synthetisiert aus operativen Beobachtungen',
            'created_by' => $user->id,
            'state'      => Draft::class,
        ]);

        if (!empty($validated['observation_id'])) {
            $obs = Observation::withoutGlobalScopes()->find($validated['observation_id']);
            if ($obs) {
                $obs->linkTo($learning, RelationType::Generates);
            }
        }

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Neue Erkenntnis '{$learning->title}' in ALF als Entwurf angelegt. Bereit zur Validierung!"
        );
    }

    /**
     * 11. ALF Step 2: Validate a Draft Learning
     */
    public function validateLearning(Request $request, ValidateLearning $action): RedirectResponse
    {
        $validated = $request->validate([
            'learning_id' => 'required|exists:learnings,id',
        ]);

        $learning = Learning::withoutGlobalScopes()->findOrFail($validated['learning_id']);
        $user = $request->user();

        $action->handle($learning, $user);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Erkenntnis '{$learning->title}' erfolgreich validiert! Kann nun zum verbindlichen Prinzip erhoben werden."
        );
    }

    /**
     * 12. ALF Step 3: Promote Validated Learning to a governing Principle
     */
    public function promoteLearning(Request $request, PromoteLearningToPrinciple $action): RedirectResponse
    {
        $validated = $request->validate([
            'learning_id'      => 'required|exists:learnings,id',
            'principle_title'  => 'nullable|string|max:255',
            'title'            => 'nullable|string|max:255',
            'statement'        => 'required|string',
            'rationale'        => 'nullable|string',
            'module_id'        => 'nullable|exists:modules,id',
            'target_module_id' => 'nullable|exists:modules,id',
        ]);

        $learning = Learning::withoutGlobalScopes()->findOrFail($validated['learning_id']);
        $user = $request->user();

        if (!($learning->state instanceof Validated)) {
            app(ValidateLearning::class)->handle($learning, $user);
            $learning->refresh();
        }

        $principleTitle = $validated['principle_title'] ?? $validated['title'] ?? $learning->title;
        $moduleId = $validated['module_id'] ?? $validated['target_module_id'] ?? null;

        $principle = $action->handle(
            learning: $learning,
            steward: $user,
            statement: $validated['statement'],
            principleTitle: $principleTitle,
            rationale: $validated['rationale'] ?? $learning->summary
        );

        if (!empty($moduleId)) {
            $module = Module::withoutGlobalScopes()->find($moduleId);
            if ($module) {
                $principle->linkTo($module, RelationType::Governs);
            }
        }

        $modMsg = !empty($module) ? " und steuert nun Modul '{$module->name}'" : '';
        return redirect()->route('dashboard')->with(
            'success',
            "✓ Grundsatz '{$principle->title}' erfolgreich als verbindliches Prinzip institutionalisiert{$modMsg}!"
        );
    }

    /**
     * 13. Ensure Canonical AMAR Template: Instantiates or updates the 10-Area Allocore Master Audit.
     */
    public function ensureAmar(
        Request $request,
        \App\Domains\Development\Services\AmarTemplateService $service
    ): RedirectResponse {
        $tenant = TenantContext::getTenant() ?? Tenant::first();
        $template = $service->ensureAmarTemplate($tenant);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Master-Vorlage '{$template->name}' mit {$template->questions->count()} Fragen über alle 10 Allocore-Bereiche bereitgestellt!"
        );
    }
}
