<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Development\Actions\RecordKpiReading;
use App\Domains\Development\Actions\RunAudit;
use App\Domains\Development\Actions\ScoreAudit;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Review\Actions\CloseReview;
use App\Domains\Review\Models\Review;
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

        $learningPayload = [
            'title'     => 'Learning from ' . $review->title,
            'summary'   => $review->summary ?? 'Key takeaways from closed review period ' . $review->period,
            'rationale' => 'Automated feedback loop closure from ARF review ' . $review->id,
        ];

        $action->handle($review, $user, $learningPayload);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Review '{$review->title}' closed! A new Draft Learning was automatically spawned into ALF."
        );
    }

    /**
     * 5. Create Review: Create a new strategic review period.
     */
    public function createReview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'period'      => 'required|string|max:50',
            'review_date' => 'required|date',
            'summary'     => 'required|string',
        ]);

        $tenant = TenantContext::getTenant() ?? Tenant::first();

        Review::create([
            'tenant_id'   => $tenant->id,
            'title'       => $validated['title'],
            'period'      => $validated['period'],
            'review_date' => $validated['review_date'],
            'summary'     => $validated['summary'],
            'status'      => Review::STATUS_OPEN,
            'created_by'  => $request->user()->id,
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ New Strategic Review '{$validated['title']}' created for period {$validated['period']}."
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
     * 6. Create / Develop New Module: Propose or develop a new operational module with initial Zielzustand.
     */
    public function createModule(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant() ?? $request->user()?->tenants()->first() ?? Tenant::first();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255',
            'description'  => 'required|string',
            'parent_id'    => 'nullable|exists:modules,id',
            'target_title' => 'nullable|string|max:255',
            'target_score' => 'nullable|numeric|min:1|max:100',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Module::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $maxOrder = Module::max('order') ?? 0;

        $module = Module::create([
            'tenant_id'   => $tenant->id,
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'],
            'parent_id'   => $validated['parent_id'] ?? null,
            'order'       => $maxOrder + 1,
        ]);

        if (!empty($validated['target_title']) || !empty($validated['target_score'])) {
            Goal::create([
                'tenant_id'    => $tenant->id,
                'module_id'    => $module->id,
                'title'        => $validated['target_title'] ?: "Zielzustand für {$module->name}",
                'description'  => "Initialer strategischer Zielzustand für das neu entwickelte Modul {$module->name}.",
                'target_score' => $validated['target_score'] ?: 85.0,
                'version'      => 1,
                'created_by'   => $request->user()->id,
            ]);
        }

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Neues Modul '{$module->name}' erfolgreich im AMF angelegt und für Reifegrad-Audits freigeschaltet."
        );
    }
}
