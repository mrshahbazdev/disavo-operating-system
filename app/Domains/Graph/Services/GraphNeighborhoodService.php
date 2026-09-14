<?php

declare(strict_types=1);

namespace App\Domains\Graph\Services;

use App\Domains\Graph\Contracts\KnowledgeNode;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Tenancy\Context\TenantContext;

class GraphNeighborhoodService
{
    /**
     * Build multi-hop graph neighborhood formatted for Cytoscape.js visualization.
     *
     * @return array{
     *     elements: array{
     *         nodes: array<int, array{data: array<string, mixed>}>,
     *         edges: array<int, array{data: array<string, mixed>}>
     *     },
     *     stats: array{
     *         center: string,
     *         depth: int,
     *         node_count: int,
     *         edge_count: int
     *     }
     * }
     */
    public function getNeighborhood(
        NodeType $type,
        int $id,
        int $depth = 3,
        ?int $tenantId = null
    ): array {
        $tenantId = $tenantId ?? TenantContext::getTenantId() ?? 1;
        $maxDepth = min(10, max(1, $depth));

        $modelClass = $type->modelClass();
        /** @var KnowledgeNode|null $centerNode */
        $centerNode = $modelClass::withoutGlobalScopes()->find($id);

        if (!$centerNode) {
            throw new \InvalidArgumentException("Node {$type->value}:{$id} not found.");
        }

        $visitedNodeIds = collect(["{$type->value}:{$id}"]);
        $currentFrontier = collect([['type' => $type->value, 'id' => $id]]);

        $collectedNodes = collect([
            $this->formatCytoscapeNode($type, $id, $centerNode, true)
        ]);

        $collectedEdges = collect();
        $seenEdgeIds = [];

        for ($level = 1; $level <= $maxDepth; $level++) {
            if ($currentFrontier->isEmpty()) {
                break;
            }

            $nextFrontier = collect();

            foreach ($currentFrontier as $item) {
                $nodeTypeStr = $item['type'];
                $nodeId = $item['id'];

                // Find active incoming & outgoing edges for this node
                $edges = KnowledgeEdge::withoutGlobalScopes()
                    ->active()
                    ->where('tenant_id', $tenantId)
                    ->where(function ($q) use ($nodeTypeStr, $nodeId) {
                        $q->where(function ($sub) use ($nodeTypeStr, $nodeId) {
                            $sub->where('source_type', $nodeTypeStr)->where('source_id', $nodeId);
                        })->orWhere(function ($sub) use ($nodeTypeStr, $nodeId) {
                            $sub->where('target_type', $nodeTypeStr)->where('target_id', $nodeId);
                        });
                    })
                    ->get();

                foreach ($edges as $edge) {
                    if (isset($seenEdgeIds[$edge->id])) {
                        continue;
                    }
                    $seenEdgeIds[$edge->id] = true;

                    $srcKey = "{$edge->source_type->value}:{$edge->source_id}";
                    $tgtKey = "{$edge->target_type->value}:{$edge->target_id}";

                    $collectedEdges->push([
                        'data' => [
                            'id'         => "edge_{$edge->id}",
                            'source'     => $srcKey,
                            'target'     => $tgtKey,
                            'label'      => $edge->relation->value,
                            'relation'   => $edge->relation->value,
                            'strength'   => $edge->strength,
                            'meta'       => $edge->meta,
                            'created_at' => $edge->created_at?->toIso8601String(),
                        ]
                    ]);

                    // Check source node
                    if (!$visitedNodeIds->contains($srcKey)) {
                        $visitedNodeIds->push($srcKey);
                        $srcModel = $edge->resolveSource();
                        if ($srcModel) {
                            $collectedNodes->push(
                                $this->formatCytoscapeNode($edge->source_type, (int) $edge->source_id, $srcModel, false)
                            );
                            $nextFrontier->push(['type' => $edge->source_type->value, 'id' => $edge->source_id]);
                        }
                    }

                    // Check target node
                    if (!$visitedNodeIds->contains($tgtKey)) {
                        $visitedNodeIds->push($tgtKey);
                        $tgtModel = $edge->resolveTarget();
                        if ($tgtModel) {
                            $collectedNodes->push(
                                $this->formatCytoscapeNode($edge->target_type, (int) $edge->target_id, $tgtModel, false)
                            );
                            $nextFrontier->push(['type' => $edge->target_type->value, 'id' => $edge->target_id]);
                        }
                    }
                }
            }

            $currentFrontier = $nextFrontier;
        }

        return [
            'elements' => [
                'nodes' => $collectedNodes->values()->all(),
                'edges' => $collectedEdges->values()->all(),
            ],
            'stats' => [
                'center'     => "{$type->value}:{$id}",
                'depth'      => $maxDepth,
                'node_count' => $collectedNodes->count(),
                'edge_count' => $collectedEdges->count(),
            ]
        ];
    }

    private function formatCytoscapeNode(
        NodeType $type,
        int $id,
        KnowledgeNode $node,
        bool $isCenter
    ): array {
        $state = null;
        if (method_exists($node, 'getAttribute') && $node->getAttribute('state')) {
            $stateAttr = $node->getAttribute('state');
            $state = is_object($stateAttr) && method_exists($stateAttr, 'name')
                ? $stateAttr->name()
                : (string) $stateAttr;
        }

        return [
            'data' => [
                'id'         => "{$type->value}:{$id}",
                'label'      => $node->nodeTitle(),
                'type'       => $type->value,
                'type_label' => $type->label(),
                'model_id'   => $id,
                'summary'    => $node->nodeSummary(),
                'is_center'  => $isCenter,
                'state'      => $state,
            ]
        ];
    }
}
