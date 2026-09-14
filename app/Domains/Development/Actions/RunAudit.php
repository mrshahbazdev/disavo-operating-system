<?php

declare(strict_types=1);

namespace App\Domains\Development\Actions;

use App\Domains\Development\Models\AuditResponse;
use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\AuditTemplate;
use App\Domains\Development\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RunAudit
{
    /**
     * Start a new audit run for a module using an audit template.
     */
    public function handle(Module $module, AuditTemplate $template, User $auditor): AuditRun
    {
        return DB::transaction(function () use ($module, $template, $auditor) {
            $run = AuditRun::create([
                'tenant_id'         => $module->tenant_id,
                'module_id'         => $module->id,
                'audit_template_id' => $template->id,
                'conducted_by'      => $auditor->id,
                'status'            => AuditRun::STATUS_IN_PROGRESS,
            ]);

            // Pre-seed responses for all questions in the template
            foreach ($template->questions as $question) {
                AuditResponse::create([
                    'audit_run_id'      => $run->id,
                    'audit_question_id' => $question->id,
                    'score'             => 0,
                ]);
            }

            return $run->load('responses.question');
        });
    }
}
