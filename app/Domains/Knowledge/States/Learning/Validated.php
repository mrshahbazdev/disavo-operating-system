<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Learning;

class Validated extends LearningState
{
    public static string $name = 'validated';

    public function name(): string
    {
        return 'validated';
    }
}
