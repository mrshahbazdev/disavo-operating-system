<?php

namespace Tests\Unit;

use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\Goal;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Knowledge\Models\Decision;
use App\Domains\Knowledge\Models\Insight;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Review\Models\Improvement;
use App\Domains\Review\Models\Review;
use App\Domains\Support\Models\DosModel;
use PHPUnit\Framework\TestCase;

class ArchitectureBoundaryTest extends TestCase
{
    public function test_all_knowledge_models_implement_knowledge_node_contract(): void
    {
        $nodeClasses = [
            // ALF
            Observation::class,
            Insight::class,
            Learning::class,
            Principle::class,
            Question::class,
            Decision::class,
            // AMF
            Module::class,
            Goal::class,
            AuditRun::class,
            Kpi::class,
            Tool::class,
            // ARF
            Review::class,
            Improvement::class,
        ];

        foreach ($nodeClasses as $class) {
            $model = new $class();
            $this->assertInstanceOf(
                KnowledgeNode::class,
                $model,
                "Model {$class} must implement KnowledgeNode contract."
            );
            $this->assertInstanceOf(
                DosModel::class,
                $model,
                "Model {$class} must inherit from DosModel."
            );
        }
    }
}
