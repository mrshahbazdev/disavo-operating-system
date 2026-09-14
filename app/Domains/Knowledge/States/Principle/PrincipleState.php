<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Principle;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class PrincipleState extends State
{
    abstract public function name(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Proposed::class)
            ->allowTransition(Proposed::class, Active::class)
            ->allowTransition(Active::class, Superseded::class)
            ->allowTransition(Active::class, Retired::class);
    }
}
