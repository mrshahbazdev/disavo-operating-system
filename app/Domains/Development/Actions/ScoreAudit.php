<?php

declare(strict_types=1);

namespace App\Domains\Development\Actions;

use App\Domains\Development\Models\AuditResponse;
use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Services\MaturityScoringService;
use App\Domains\Graph\Enums\RelationType;
use Illuminate\Support\Facades\DB;

class ScoreAudit
{
    public function __construct(
        protected MaturityScoringService $scoringService
    ) {}

    /**
     * Submit response scores, finalize AuditRun, and establish Measures edge to Module.
     *
     * @param array<int, array{score: int, notes?: ?string, evidence?: ?string}> $responses Keyed by question_id
     */
    public function handle(AuditRun $run, array $responses): AuditRun
    {
        return DB::transaction(function () use ($run, $responses) {
            foreach ($responses as $questionId => $data) {
                AuditResponse::updateOrCreate(
                    [
                        'audit_run_id'      => $run->id,
                        'audit_question_id' => $questionId,
                    ],
                    [
                        'score'    => (int) $data['score'],
                        'notes'    => $data['notes'] ?? null,
                        'evidence' => $data['evidence'] ?? null,
                    ]
                );
            }

            // Recalculate overall score with database weights
            $score = $this->scoringService->calculateAuditScore($run);

            $run->update([
                'status'        => AuditRun::STATUS_COMPLETED,
                'overall_score' => $score,
                'completed_at'  => now(),
            ]);

            // Graph connection: AuditRun measures Module
            $run->linkTo(
                $run->module,
                RelationType::Measures,
                [
                    'overall_score' => $score,
                    'audited_at'    => now()->toIso8601String(),
                ]
            );

            return $run->fresh(['responses.question', 'module']);
        });
    }
}
