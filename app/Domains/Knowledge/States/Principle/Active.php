<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Principle;

class Active extends PrincipleState
{
    public static string $name = 'active';

    public function name(): string
    {
        return 'active';
    }
}
