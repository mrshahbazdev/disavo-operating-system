<?php

declare(strict_types=1);

namespace App\Domains\Graph\Commands;

use App\Domains\Graph\Services\OrphanDetectionService;
use Illuminate\Console\Command;

class DetectOrphansCommand extends Command
{
    protected $signature = 'dos:graph:orphans {--tenant= : Specific tenant ID to inspect}';

    protected $description = 'Detect and report isolated knowledge nodes with zero active graph edges';

    public function handle(OrphanDetectionService $service): int
    {
        $tenantId = $this->option('tenant') ? (int) $this->option('tenant') : null;

        $this->info('Scanning knowledge graph for orphan nodes...');

        $report = $service->detectOrphans($tenantId);

        if ($report['total_orphans'] === 0) {
            $this->info('No orphan nodes found. Graph is completely interconnected!');
            return 0;
        }

        $this->warn("Found {$report['total_orphans']} orphan node(s) across the system.");

        $summaryData = [];
        foreach ($report['by_type'] as $type => $count) {
            $summaryData[] = [$type, $count];
        }
        $this->table(['Node Type', 'Orphan Count'], $summaryData);

        if ($this->output->isVerbose()) {
            $details = $report['orphans']->map(fn ($o) => [
                $o['id'],
                $o['type'],
                $o['title'],
                $o['created_at'] ?? 'N/A',
            ])->all();

            $this->line('');
            $this->table(['Node ID', 'Type', 'Title', 'Created At'], $details);
        } else {
            $this->comment('Use -v to list all individual orphan nodes.');
        }

        return 0;
    }
}
