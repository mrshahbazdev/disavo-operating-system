<?php

declare(strict_types=1);

namespace App\Domains\Graph\Actions;

use App\Domains\Graph\Models\KnowledgeEdge;
use App\Models\User;
use InvalidArgumentException;

class InvalidateEdge
{
    public function handle(KnowledgeEdge $edge, User $user, string $reason): KnowledgeEdge
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('An invalidation reason must be documented.');
        }

        $edge->invalidate($user, $reason);

        return $edge;
    }
}
