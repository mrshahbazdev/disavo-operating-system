<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Learning;

class Rejected extends LearningState
{
    public static string $name = 'rejected';

    public function name(): string
    {
        return 'rejected';
    }
}
