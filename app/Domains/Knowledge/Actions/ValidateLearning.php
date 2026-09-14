<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Actions;

use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\States\Learning\Validated;
use App\Models\User;

class ValidateLearning
{
    public function handle(Learning $learning, User $validator): Learning
    {
        $learning->state->transitionTo(Validated::class);
        $learning->update([
            'validated_at' => now(),
            'validated_by' => $validator->id,
        ]);

        return $learning->fresh();
    }
}
