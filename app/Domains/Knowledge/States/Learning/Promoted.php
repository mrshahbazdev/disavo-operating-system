<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Learning;

class Promoted extends LearningState
{
    public static string $name = 'promoted';

    public function name(): string
    {
        return 'promoted';
    }
}
