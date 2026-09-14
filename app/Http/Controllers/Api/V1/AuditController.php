<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Development\Actions\RunAudit;
use App\Domains\Development\Actions\ScoreAudit;
use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Module;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function runAudit(
        Module $module,
        AuditTemplate $template,
        Request $request,
        RunAudit $action
    ): JsonResponse {
        $user = $request->user() ?? \App\Models\User::firstOrFail();
        $run = $action->handle($module, $template, $user);

        return response()->json($run, 201);
    }

    public function scoreAudit(
        AuditRun $auditRun,
        Request $request,
        ScoreAudit $action
    ): JsonResponse {
        $validated = $request->validate([
            'responses'                 => 'required|array',
            'responses.*.score'         => 'required|integer|min:0|max:10',
            'responses.*.notes'         => 'nullable|string',
            'responses.*.evidence'      => 'nullable|string',
        ]);

        $scoredRun = $action->handle($auditRun, $validated['responses']);

        return response()->json([
            'message'   => 'Audit evaluated and scored successfully. Measures graph edge established.',
            'audit_run' => $scoredRun,
        ]);
    }
}
