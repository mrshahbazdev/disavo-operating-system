<?php

declare(strict_types=1);

namespace App\Domains\Graph\Enums;

use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Knowledge\Models\Decision;
use App\Domains\Knowledge\Models\Insight;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Review\Models\Improvement;
use App\Domains\Review\Models\Review;

enum NodeType: string
{
    // ALF — Knowledge Nodes
    case Observation = 'observation';
    case Insight     = 'insight';
    case Learning    = 'learning';
    case Principle   = 'principle';
    case Question    = 'question';
    case Decision    = 'decision';

    // AMF — Development Nodes
    case Module      = 'module';
    case Goal        = 'goal';
    case Audit       = 'audit';
    case Kpi         = 'kpi';
    case Tool        = 'tool';

    // ARF — Review Nodes
    case Review      = 'review';
    case Improvement = 'improvement';

    public function modelClass(): string
    {
        return match ($this) {
            self::Observation => Observation::class,
            self::Insight     => Insight::class,
            self::Learning    => Learning::class,
            self::Principle   => Principle::class,
            self::Question    => Question::class,
            self::Decision    => Decision::class,
            self::Module      => Module::class,
            self::Goal        => Goal::class,
            self::Audit       => AuditRun::class,
            self::Kpi         => Kpi::class,
            self::Tool        => Tool::class,
            self::Review      => Review::class,
            self::Improvement => Improvement::class,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Observation => 'Beobachtung (Observation)',
            self::Insight     => 'Erkenntnis (Insight)',
            self::Learning    => 'Learning',
            self::Principle   => 'Prinzip (Principle)',
            self::Question    => 'Offene Frage (Question)',
            self::Decision    => 'Entscheidung (Decision)',
            self::Module      => 'Modul (Module)',
            self::Goal        => 'Zielzustand (Goal)',
            self::Audit       => 'Audit',
            self::Kpi         => 'KPI',
            self::Tool        => 'Werkzeug (Tool)',
            self::Review      => 'Review',
            self::Improvement => 'Verbesserung (Improvement)',
        };
    }
}
