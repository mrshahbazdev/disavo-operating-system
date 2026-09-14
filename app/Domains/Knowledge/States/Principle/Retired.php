<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Principle;

class Retired extends PrincipleState
{
    public static string $name = 'retired';

    public function name(): string
    {
        return 'retired';
    }
}
