<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Learning;

class Deprecated extends LearningState
{
    public static string $name = 'deprecated';

    public function name(): string
    {
        return 'deprecated';
    }
}
