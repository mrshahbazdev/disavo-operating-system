<?php

declare(strict_types=1);

namespace App\Domains\Graph\Services;

use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Tenancy\Context\TenantContext;
use Illuminate\Support\Collection;

class OrphanDetectionService
{
    /**
     * Identify all nodes with zero incoming and zero outgoing active edges.
     *
     * @return array{
     *     total_orphans: int,
     *     by_type: array<string, int>,
     *     orphans: Collection<int, array{id: string, type: string, type_label: string, model_id: int, title: string, created_at: ?string}>
     * }
     */
    public function detectOrphans(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? TenantContext::getTenantId() ?? 1;

        $orphans = collect();
        $byType = [];

        foreach (NodeType::cases() as $nodeType) {
            $modelClass = $nodeType->modelClass();
            $table = (new $modelClass())->getTable();

            // Find nodes of this type that have NO active edges as source OR target
            $candidates = $modelClass::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->whereNull('deleted_at')
                ->whereNotExists(function ($query) use ($nodeType, $tenantId, $table) {
                    $query->selectRaw(1)
                        ->from('knowledge_edges')
                        ->where('tenant_id', $tenantId)
                        ->whereNull('invalidated_at')
                        ->where(function ($q) use ($nodeType, $table) {
                            $q->where(function ($sub) use ($nodeType, $table) {
                                $sub->where('source_type', $nodeType->value)
                                    ->whereColumn('source_id', "{$table}.id");
                            })->orWhere(function ($sub) use ($nodeType, $table) {
                                $sub->where('target_type', $nodeType->value)
                                    ->whereColumn('target_id', "{$table}.id");
                            });
                        });
                })
                ->get();

            $byType[$nodeType->value] = $candidates->count();

            foreach ($candidates as $candidate) {
                $orphans->push([
                    'id'         => "{$nodeType->value}:{$candidate->id}",
                    'type'       => $nodeType->value,
                    'type_label' => $nodeType->label(),
                    'model_id'   => $candidate->id,
                    'title'      => $candidate->nodeTitle(),
                    'created_at' => $candidate->created_at?->toIso8601String(),
                ]);
            }
        }

        return [
            'total_orphans' => $orphans->count(),
            'by_type'       => array_filter($byType),
            'orphans'       => $orphans,
        ];
    }
}
