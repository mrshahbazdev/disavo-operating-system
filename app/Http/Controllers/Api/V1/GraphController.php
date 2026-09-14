<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Graph\Actions\CreateEdge;
use App\Domains\Graph\Actions\InvalidateEdge;
use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Enums\RelationType;
use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Graph\Services\ContradictionDetectionService;
use App\Domains\Graph\Services\GraphNeighborhoodService;
use App\Domains\Graph\Services\OrphanDetectionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class GraphController extends Controller
{
    /**
     * Return multi-hop graph neighborhood formatted for Cytoscape.js visualization.
     */
    public function nodeGraph(string $type, int $id, Request $request, GraphNeighborhoodService $service): JsonResponse
    {
        $nodeType = NodeType::tryFrom($type);
        if ($nodeType === null) {
            return response()->json(['error' => "Invalid node type '{$type}'"], 422);
        }

        $depth = max(1, min(10, (int) $request->query('depth', 3)));
        $tenantId = $request->user()?->tenant_id;

        try {
            $data = $service->getNeighborhood($nodeType, $id, $depth, $tenantId);
            return response()->json($data);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Surface active contradictions and tensions between Learnings and Principles.
     */
    public function contradictions(Request $request, ContradictionDetectionService $service): JsonResponse
    {
        $tenantId = $request->user()?->tenant_id;
        $tensions = $service->getActiveContradictions($tenantId);

        return response()->json([
            'data'  => $tensions,
            'count' => $tensions->count(),
        ]);
    }

    /**
     * Surface isolated knowledge nodes with zero active incoming and outgoing edges.
     */
    public function orphans(Request $request, OrphanDetectionService $service): JsonResponse
    {
        $tenantId = $request->user()?->tenant_id;
        $report = $service->detectOrphans($tenantId);

        return response()->json($report);
    }


    public function createEdge(Request $request, CreateEdge $action): JsonResponse
    {
        $validated = $request->validate([
            'source_type' => 'required|string',
            'source_id'   => 'required|integer',
            'target_type' => 'required|string',
            'target_id'   => 'required|integer',
            'relation'    => 'required|string',
            'strength'    => 'nullable|integer|min:0|max:100',
            'meta'        => 'nullable|array',
        ]);

        $sourceType = NodeType::tryFrom($validated['source_type']);
        $targetType = NodeType::tryFrom($validated['target_type']);
        $relation   = RelationType::tryFrom($validated['relation']);

        if (!$sourceType || !$targetType || !$relation) {
            return response()->json(['error' => 'Invalid enum types supplied.'], 422);
        }

        $sourceClass = $sourceType->modelClass();
        $targetClass = $targetType->modelClass();

        $source = $sourceClass::findOrFail($validated['source_id']);
        $target = $targetClass::findOrFail($validated['target_id']);

        try {
            $edge = $action->handle(
                source: $source,
                target: $target,
                relation: $relation,
                meta: $validated['meta'] ?? [],
                strength: $validated['strength'] ?? 100,
                createdBy: $request->user()?->id ?? 1
            );
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json($edge, 201);
    }

    public function invalidateEdge(
        KnowledgeEdge $edge,
        Request $request,
        InvalidateEdge $action
    ): JsonResponse {
        $validated = $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $user = $request->user() ?? \App\Models\User::firstOrFail();
        $invalidated = $action->handle($edge, $user, $validated['reason']);

        return response()->json([
            'message' => 'Edge invalidated. Preserved in history.',
            'edge'    => $invalidated,
        ]);
    }
}
