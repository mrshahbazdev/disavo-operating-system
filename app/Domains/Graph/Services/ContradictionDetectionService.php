<?php

declare(strict_types=1);

namespace App\Domains\Graph\Services;

use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Knowledge\States\Principle\Active;
use App\Domains\Tenancy\Context\TenantContext;
use Illuminate\Support\Collection;

class ContradictionDetectionService
{
    /**
     * Detect and surface all active tensions in the knowledge graph.
     *
     * @return Collection<int, array{
     *     edge_id: int,
     *     tension_severity: string,
     *     learning: array{id: int, title: string, state: string, summary: ?string},
     *     principle: array{id: int, title: string, version: int, state: string, statement: string},
     *     governed_modules: array<int, array{id: int, name: string}>,
     *     detected_at: string,
     *     meta: ?array
     * }>
     */
    public function getActiveContradictions(?int $tenantId = null): Collection
    {
        $tenantId = $tenantId ?? TenantContext::getTenantId() ?? 1;

        $edges = KnowledgeEdge::withoutGlobalScopes()
            ->active()
            ->where('tenant_id', $tenantId)
            ->where('relation', RelationType::Contradicts->value)
            ->get();

        return $edges->map(function (KnowledgeEdge $edge) {
            $learning = Learning::withoutGlobalScopes()->find($edge->source_id);
            $principle = Principle::withoutGlobalScopes()->find($edge->target_id);

            if (!$learning || !$principle) {
                return null;
            }

            // Find modules governed by this contradicted principle
            $governedModules = $principle->outgoingEdges()
                ->where('relation', RelationType::Governs->value)
                ->get()
                ->map(fn (KnowledgeEdge $e) => $e->resolveTarget())
                ->filter()
                ->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])
                ->values()
                ->all();

            $isPrincipleActive = ($principle->state instanceof Active);

            return [
                'edge_id'          => $edge->id,
                'tension_severity' => $isPrincipleActive ? 'critical' : 'resolved',
                'learning'         => [
                    'id'      => $learning->id,
                    'title'   => $learning->title,
                    'state'   => $learning->state->name(),
                    'summary' => $learning->summary,
                ],
                'principle'        => [
                    'id'        => $principle->id,
                    'title'     => $principle->title,
                    'version'   => $principle->version,
                    'state'     => $principle->state->name(),
                    'statement' => $principle->statement,
                ],
                'governed_modules' => $governedModules,
                'detected_at'      => $edge->created_at?->toIso8601String() ?? now()->toIso8601String(),
                'meta'             => $edge->meta,
            ];
        })->filter()->values();
    }
}
