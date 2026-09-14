<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\States\Learning;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class LearningState extends State
{
    abstract public function name(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, UnderValidation::class)
            ->allowTransition(Draft::class, Validated::class)
            ->allowTransition(UnderValidation::class, Validated::class)
            ->allowTransition(UnderValidation::class, Rejected::class)
            ->allowTransition(Validated::class, Promoted::class)
            ->allowTransition(Validated::class, Deprecated::class);
    }
}
