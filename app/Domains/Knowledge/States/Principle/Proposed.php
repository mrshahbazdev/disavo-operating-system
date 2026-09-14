<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Principle;

class Proposed extends PrincipleState
{
    public static string $name = 'proposed';

    public function name(): string
    {
        return 'proposed';
    }
}
