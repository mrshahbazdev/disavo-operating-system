<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Principle;

class Superseded extends PrincipleState
{
    public static string $name = 'superseded';

    public function name(): string
    {
        return 'superseded';
    }
}
