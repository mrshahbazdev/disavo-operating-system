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
     * @param array<int, array{score?: int|numeric, binary_answer?: bool|int|string, status_symbol?: ?string, notes?: ?string, evidence?: ?string}> $responses Keyed by question_id
     * @param array{
     *     version?: ?string,
     *     user_paths_audited?: ?int,
     *     tools_audited?: ?int,
     *     previous_score?: ?float,
     *     entrepreneur_test_passed?: ?bool,
     *     new_feature_ban?: ?bool,
     *     insight_protocols?: ?array,
     *     cybernetic_feedback?: ?array,
     *     quotas?: ?array
     * } $extraMeta
     */
    public function handle(AuditRun $run, array $responses, array $extraMeta = []): AuditRun
    {
        return DB::transaction(function () use ($run, $responses, $extraMeta) {
            foreach ($responses as $questionId => $data) {
                $binary = isset($data['binary_answer']) ? filter_var($data['binary_answer'], FILTER_VALIDATE_BOOLEAN) : null;
                $score = isset($data['score']) ? (int) $data['score'] : ($binary ? 1 : 0);

                AuditResponse::updateOrCreate(
                    [
                        'audit_run_id'      => $run->id,
                        'audit_question_id' => $questionId,
                    ],
                    [
                        'score'         => $score,
                        'status_symbol' => $data['status_symbol'] ?? null,
                        'binary_answer' => $binary,
                        'notes'         => $data['notes'] ?? null,
                        'evidence'      => $data['evidence'] ?? null,
                    ]
                );
            }

            // Recalculate overall score with database weights
            $score = $this->scoringService->calculateAuditScore($run);
            $areaScores = $this->scoringService->calculateAreaScores($run);

            $currentMeta = $run->meta ?? [];
            $mergedMeta = array_merge($currentMeta, [
                'version'             => $extraMeta['version'] ?? $run->version ?? '1.0',
                'user_paths_audited'  => $extraMeta['user_paths_audited'] ?? $run->user_paths_audited ?? 1,
                'tools_audited'       => $extraMeta['tools_audited'] ?? $run->tools_audited ?? 0,
                'previous_score'      => $extraMeta['previous_score'] ?? $run->previous_score ?? null,
                'area_scores'         => $areaScores,
                'insight_protocols'   => $extraMeta['insight_protocols'] ?? $currentMeta['insight_protocols'] ?? [],
                'cybernetic_feedback' => $extraMeta['cybernetic_feedback'] ?? $currentMeta['cybernetic_feedback'] ?? [],
                'quotas'              => $extraMeta['quotas'] ?? $currentMeta['quotas'] ?? [],
            ]);

            $run->update([
                'status'                   => AuditRun::STATUS_COMPLETED,
                'overall_score'            => $score,
                'completed_at'             => now(),
                'version'                  => $extraMeta['version'] ?? $run->version ?? '1.0',
                'user_paths_audited'       => $extraMeta['user_paths_audited'] ?? $run->user_paths_audited ?? 1,
                'tools_audited'            => $extraMeta['tools_audited'] ?? $run->tools_audited ?? 0,
                'previous_score'           => $extraMeta['previous_score'] ?? $run->previous_score ?? null,
                'entrepreneur_test_passed' => isset($extraMeta['entrepreneur_test_passed']) ? (bool) $extraMeta['entrepreneur_test_passed'] : (bool) $run->entrepreneur_test_passed,
                'new_feature_ban'          => isset($extraMeta['new_feature_ban']) ? (bool) $extraMeta['new_feature_ban'] : (bool) $run->new_feature_ban,
                'meta'                     => $mergedMeta,
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
