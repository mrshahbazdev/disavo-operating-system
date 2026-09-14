<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Learning;

class UnderValidation extends LearningState
{
    public static string $name = 'under_validation';

    public function name(): string
    {
        return 'under_validation';
    }
}
