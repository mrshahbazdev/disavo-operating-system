<?php

declare(strict_types=1);

namespace App\Domains\Development\Services;

use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Module;

class MaturityScoringService
{
    /**
     * Calculate database-weighted overall score (0.00 to 100.00%) for an AuditRun.
     */
    public function calculateAuditScore(AuditRun $run): float
    {
        $responses = $run->responses()->with('question')->get();

        if ($responses->isEmpty()) {
            return 0.0;
        }

        $totalWeightedScore = 0.0;
        $totalWeight = 0;

        foreach ($responses as $response) {
            $question = $response->question;
            if (!$question) {
                continue;
            }

            $weight = max(1, (int) $question->weight);
            $maxScore = max(1, (int) $question->max_score);

            if ($question->isBinary()) {
                $percentage = $response->binary_answer ? 1.0 : 0.0;
            } else {
                $actualScore = min($maxScore, max(0, (int) $response->score));
                $percentage = $actualScore / $maxScore;
            }

            $totalWeightedScore += ($percentage * $weight);
            $totalWeight += $weight;
        }

        if ($totalWeight === 0) {
            return 0.0;
        }

        return round(($totalWeightedScore / $totalWeight) * 100, 2);
    }

    /**
     * Calculate individual area scores for multi-area audits like AMAR.
     *
     * @return array<string, array{score: float, total_questions: int, completed: int}>
     */
    public function calculateAreaScores(AuditRun $run): array
    {
        $responses = $run->responses()->with('question')->get();
        $areaMap = [];

        foreach ($responses as $response) {
            $question = $response->question;
            if (!$question || empty($question->area)) {
                continue;
            }

            $area = $question->area;
            if (!isset($areaMap[$area])) {
                $areaMap[$area] = [
                    'weighted_score'  => 0.0,
                    'total_weight'    => 0,
                    'total_questions' => 0,
                    'completed'       => 0,
                ];
            }

            $weight = max(1, (int) $question->weight);
            $maxScore = max(1, (int) $question->max_score);

            if ($question->isBinary()) {
                $percentage = $response->binary_answer ? 1.0 : 0.0;
            } else {
                $actualScore = min($maxScore, max(0, (int) $response->score));
                $percentage = $actualScore / $maxScore;
            }

            $areaMap[$area]['weighted_score'] += ($percentage * $weight);
            $areaMap[$area]['total_weight'] += $weight;
            $areaMap[$area]['total_questions']++;
            $areaMap[$area]['completed']++;
        }

        $result = [];
        foreach ($areaMap as $area => $data) {
            $score = $data['total_weight'] > 0
                ? round(($data['weighted_score'] / $data['total_weight']) * 100, 2)
                : 0.0;
            $result[$area] = [
                'score'           => $score,
                'total_questions' => $data['total_questions'],
                'completed'       => $data['completed'],
            ];
        }

        return $result;
    }

    /**
     * Calculate maturity delta between latest completed audit and target Zielzustand (Goal).
     *
     * @return array{
     *     module_id: int,
     *     module_name: string,
     *     current_score: float,
     *     target_score: float,
     *     gap: float,
     *     status: string,
     *     last_audited_at: ?string
     * }
     */
    public function getMaturityDelta(Module $module): array
    {
        $latestAudit = $module->auditRuns()
            ->where('status', AuditRun::STATUS_COMPLETED)
            ->orderByDesc('completed_at')
            ->first();

        $latestGoal = $module->goals()
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->first();

        $currentScore = $latestAudit?->overall_score ? (float) $latestAudit->overall_score : 0.0;
        $targetScore = $latestGoal?->target_score ? (float) $latestGoal->target_score : 100.0;
        $gap = round($targetScore - $currentScore, 2);

        return [
            'module_id'       => (int) $module->id,
            'module_name'     => $module->name,
            'current_score'   => $currentScore,
            'target_score'    => $targetScore,
            'gap'             => $gap,
            'status'          => $gap <= 0.0 ? 'target_achieved' : 'gap_detected',
            'last_audited_at' => $latestAudit?->completed_at?->toIso8601String(),
        ];
    }
}
