<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Actions;

use App\Domains\Graph\Enums\RelationType;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\States\Principle\Active;
use App\Domains\Knowledge\States\Principle\Superseded;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SupersedePrinciple
{
    /**
     * Supersede an existing Principle with an updated version.
     * Generates a new Principle node, connects with 'Supersedes' edge, and updates states.
     */
    public function handle(
        Principle $oldPrinciple,
        User $actor,
        string $newStatement,
        string $changeReason,
        ?string $newTitle = null,
        ?string $newRationale = null
    ): Principle {
        if (!($oldPrinciple->state instanceof Active)) {
            throw new InvalidArgumentException(
                "Only active principles can be superseded. Current state is '{$oldPrinciple->state->name()}'."
            );
        }

        return DB::transaction(function () use ($oldPrinciple, $actor, $newStatement, $changeReason, $newTitle, $newRationale) {
            $newPrinciple = Principle::create([
                'tenant_id'  => $oldPrinciple->tenant_id,
                'title'      => $newTitle ?? $oldPrinciple->title,
                'statement'  => $newStatement,
                'rationale'  => $newRationale ?? $oldPrinciple->rationale,
                'state'      => Active::class,
                'version'    => $oldPrinciple->version + 1,
                'created_by' => $actor->id,
            ]);

            // Graph edge: newPrinciple supersedes oldPrinciple
            $newPrinciple->linkTo(
                $oldPrinciple,
                RelationType::Supersedes,
                [
                    'reason'       => $changeReason,
                    'superseded_by' => $actor->id,
                    'superseded_at' => now()->toIso8601String(),
                ]
            );

            // Transition old principle to Superseded
            $oldPrinciple->state->transitionTo(Superseded::class);
            $oldPrinciple->update([
                'superseded_by_id' => $newPrinciple->id,
            ]);

            return $newPrinciple;
        });
    }
}
