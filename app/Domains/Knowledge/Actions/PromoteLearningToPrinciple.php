<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Actions;

use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\States\Learning\Promoted;
use App\Domains\Knowledge\States\Learning\Validated;
use App\Domains\Knowledge\States\Principle\Active;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PromoteLearningToPrinciple
{
    /**
     * Promote a Validated Learning into an operational Principle, recording the Promotes edge.
     */
    public function handle(
        Learning $learning,
        User $steward,
        string $statement,
        ?string $principleTitle = null,
        ?string $rationale = null
    ): Principle {
        if (!($learning->state instanceof Validated)) {
            throw new InvalidArgumentException(
                "Only validated learnings can be promoted to principles. Current state is '{$learning->state->name()}'."
            );
        }

        return DB::transaction(function () use ($learning, $steward, $statement, $principleTitle, $rationale) {
            $principle = Principle::create([
                'tenant_id'  => $learning->tenant_id,
                'title'      => $principleTitle ?? $learning->title,
                'statement'  => $statement,
                'rationale'  => $rationale ?? $learning->rationale ?? $learning->summary,
                'state'      => Active::class,
                'version'    => 1,
                'created_by' => $steward->id,
            ]);

            // Link Learning → Principle via 'Promotes' edge
            $learning->linkTo(
                $principle,
                RelationType::Promotes,
                [
                    'promoted_by_user_id' => $steward->id,
                    'promoted_by_name'    => $steward->name,
                    'promoted_at'         => now()->toIso8601String(),
                ]
            );

            // Transition Learning state to Promoted
            $learning->state->transitionTo(Promoted::class);
            $learning->update([
                'promoted_at' => now(),
                'promoted_by' => $steward->id,
            ]);

            return $principle;
        });
    }
}
