<?php

declare(strict_types=1);

namespace App\Domains\Review\Actions;

use App\Domains\Review\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use LogicException;

class CloseReview
{
    public function __construct(
        protected SpawnLearningFromReview $spawnLearning
    ) {}

    /**
     * Close a review and optionally spawn a Learning from a critical finding.
     *
     * @param array{title: string, summary: string, rationale?: ?string}|null $learningPayload
     */
    public function handle(
        Review $review,
        User $closer,
        ?array $learningPayload = null
    ): Review {
        if ($review->status === Review::STATUS_CLOSED) {
            throw new LogicException('Review is already closed.');
        }

        return DB::transaction(function () use ($review, $closer, $learningPayload) {
            $review->update([
                'status'    => Review::STATUS_CLOSED,
                'closed_at' => now(),
                'closed_by' => $closer->id,
            ]);

            // If a learning payload was supplied, close the feedback loop immediately
            if ($learningPayload !== null) {
                $this->spawnLearning->handle(
                    review: $review,
                    actor: $closer,
                    learningTitle: $learningPayload['title'],
                    summary: $learningPayload['summary'],
                    rationale: $learningPayload['rationale'] ?? null
                );
            }

            return $review->fresh(['items', 'improvements']);
        });
    }
}
