<?php

declare(strict_types=1);

namespace App\Domains\Graph\Commands;

use App\Domains\Development\Models\AuditRun;
use App\Domains\Development\Models\Kpi;
use App\Domains\Development\Models\Module;
use App\Domains\Development\Models\Tool;
use App\Domains\Graph\Actions\CreateEdge;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Review\Models\ReviewItem;
use Illuminate\Console\Command;

class BackfillKnowledgeEdgesCommand extends Command
{
    protected $signature = 'dos:graph:backfill {--tenant= : Specific tenant ID to backfill}';

    protected $description = 'Backfill missing knowledge edges from relational foreign keys';

    public function handle(CreateEdge $createEdge): int
    {
        $tenantId = $this->option('tenant') ? (int) $this->option('tenant') : null;

        $this->info('Starting knowledge edge backfill...');
        $totalCreated = 0;

        // 1. AuditRun -> Module (measures)
        $auditQuery = AuditRun::withoutGlobalScopes()->whereNotNull('module_id');
        if ($tenantId) {
            $auditQuery->where('tenant_id', $tenantId);
        }
        $audits = $auditQuery->get();
        $auditCount = 0;
        foreach ($audits as $audit) {
            $module = Module::withoutGlobalScopes()->find($audit->module_id);
            if ($module && $this->edgeDoesNotExist($audit, $module, RelationType::Measures)) {
                $createEdge->handle($audit, $module, RelationType::Measures, ['backfilled' => true]);
                $auditCount++;
            }
        }
        $this->line("  - Measures (AuditRun -> Module): {$auditCount} edges created");
        $totalCreated += $auditCount;

        // 2. Kpi -> Module (quantifies)
        $kpiQuery = Kpi::withoutGlobalScopes()->whereNotNull('module_id');
        if ($tenantId) {
            $kpiQuery->where('tenant_id', $tenantId);
        }
        $kpis = $kpiQuery->get();
        $kpiCount = 0;
        foreach ($kpis as $kpi) {
            $module = Module::withoutGlobalScopes()->find($kpi->module_id);
            if ($module && $this->edgeDoesNotExist($kpi, $module, RelationType::Quantifies)) {
                $createEdge->handle($kpi, $module, RelationType::Quantifies, ['backfilled' => true]);
                $kpiCount++;
            }
        }
        $this->line("  - Quantifies (Kpi -> Module): {$kpiCount} edges created");
        $totalCreated += $kpiCount;

        // 3. Tool -> Module (improves)
        $toolQuery = Tool::withoutGlobalScopes()->whereNotNull('module_id');
        if ($tenantId) {
            $toolQuery->where('tenant_id', $tenantId);
        }
        $tools = $toolQuery->get();
        $toolCount = 0;
        foreach ($tools as $tool) {
            $module = Module::withoutGlobalScopes()->find($tool->module_id);
            if ($module && $this->edgeDoesNotExist($tool, $module, RelationType::Improves)) {
                $createEdge->handle($tool, $module, RelationType::Improves, ['backfilled' => true]);
                $toolCount++;
            }
        }
        $this->line("  - Improves (Tool -> Module): {$toolCount} edges created");
        $totalCreated += $toolCount;

        // 4. Review -> Module (evaluates) via ReviewItem
        $itemQuery = ReviewItem::with(['review', 'module'])->whereNotNull('module_id');
        if ($tenantId) {
            $itemQuery->whereHas('review', fn ($q) => $q->where('tenant_id', $tenantId));
        }
        $items = $itemQuery->get();
        $reviewCount = 0;
        foreach ($items as $item) {
            if ($item->review && $item->module && $this->edgeDoesNotExist($item->review, $item->module, RelationType::Evaluates)) {
                $createEdge->handle($item->review, $item->module, RelationType::Evaluates, ['backfilled' => true]);
                $reviewCount++;
            }
        }
        $this->line("  - Evaluates (Review -> Module): {$reviewCount} edges created");
        $totalCreated += $reviewCount;

        // 5. Principle -> Principle (supersedes)
        $principleQuery = Principle::withoutGlobalScopes()->whereNotNull('superseded_by_id');
        if ($tenantId) {
            $principleQuery->where('tenant_id', $tenantId);
        }
        $supersededPrinciples = $principleQuery->get();
        $supersedesCount = 0;
        foreach ($supersededPrinciples as $oldPrinciple) {
            $newPrinciple = Principle::withoutGlobalScopes()->find($oldPrinciple->superseded_by_id);
            if ($newPrinciple && $this->edgeDoesNotExist($newPrinciple, $oldPrinciple, RelationType::Supersedes)) {
                $createEdge->handle($newPrinciple, $oldPrinciple, RelationType::Supersedes, ['backfilled' => true]);
                $supersedesCount++;
            }
        }
        $this->line("  - Supersedes (Principle -> Principle): {$supersedesCount} edges created");
        $totalCreated += $supersedesCount;

        $this->info("Backfill complete. Total edges created: {$totalCreated}");

        return 0;
    }

    private function edgeDoesNotExist($source, $target, RelationType $relation): bool
    {
        return !KnowledgeEdge::withoutGlobalScopes()
            ->active()
            ->where('tenant_id', $source->tenant_id)
            ->where('source_type', $source->nodeType()->value)
            ->where('source_id', $source->getKey())
            ->where('target_type', $target->nodeType()->value)
            ->where('target_id', $target->getKey())
            ->where('relation', $relation->value)
            ->exists();
    }
}
