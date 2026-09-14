<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Learning;

class Draft extends LearningState
{
    public static string $name = 'draft';

    public function name(): string
    {
        return 'draft';
    }
}
