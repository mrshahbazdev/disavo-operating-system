<?php

declare(strict_types=1);

namespace App\Domains\Review\Actions;

use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Review\Models\Improvement;
use App\Domains\Review\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SpawnLearningFromReview
{
    /**
     * Closes the organizational feedback loop:
     * Review → (Generates) → Learning (Draft state in ALF).
     */
    public function handle(
        Review $review,
        User $actor,
        string $learningTitle,
        string $summary,
        ?string $rationale = null,
        ?Improvement $improvement = null
    ): Learning {
        return DB::transaction(function () use ($review, $actor, $learningTitle, $summary, $rationale, $improvement) {
            $learning = Learning::create([
                'tenant_id'  => $review->tenant_id,
                'title'      => $learningTitle,
                'summary'    => $summary,
                'rationale'  => $rationale ?? $review->summary,
                'state'      => Draft::class,
                'created_by' => $actor->id,
            ]);

            // Graph connection: Review generates Learning
            $review->linkTo(
                $learning,
                RelationType::Generates,
                [
                    'review_title'    => $review->title,
                    'period'          => $review->period,
                    'improvement_id'  => $improvement?->id,
                    'spawned_by_user' => $actor->id,
                    'spawned_at'      => now()->toIso8601String(),
                ]
            );

            return $learning;
        });
    }
}
