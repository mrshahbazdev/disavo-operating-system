<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Graph\Enums\NodeType;
use App\Domains\Graph\Queries\ImpactRadiusQuery;
use App\Domains\Graph\Queries\ProvenancePathQuery;
use App\Domains\Knowledge\Actions\SupersedePrinciple;
use App\Domains\Knowledge\Models\Principle;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PrincipleController extends Controller
{
    public function index(): JsonResponse
    {
        $principles = QueryBuilder::for(Principle::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('state'),
            ])
            ->allowedSorts(['id', 'version', 'created_at', 'title'])
            ->with(['creator', 'supersededBy'])
            ->paginate(request()->integer('per_page', 25));

        return response()->json($principles);
    }

    public function show(Principle $principle): JsonResponse
    {
        $principle->load(['creator', 'supersededBy', 'previousVersions', 'outgoingEdges', 'incomingEdges']);

        return response()->json($principle);
    }

    public function provenance(
        Principle $principle,
        ProvenancePathQuery $query
    ): JsonResponse {
        $trail = $query->trace(
            type: NodeType::Principle,
            id: (int) $principle->id,
            tenantId: (int) $principle->tenant_id
        );

        return response()->json([
            'principle' => $principle,
            'trail'     => $trail,
        ]);
    }

    public function impact(
        Principle $principle,
        ImpactRadiusQuery $query
    ): JsonResponse {
        $downstream = $query->trace(
            type: NodeType::Principle,
            id: (int) $principle->id,
            tenantId: (int) $principle->tenant_id
        );

        return response()->json([
            'principle'  => $principle,
            'downstream' => $downstream,
        ]);
    }

    public function supersede(
        Principle $principle,
        Request $request,
        SupersedePrinciple $action
    ): JsonResponse {
        $validated = $request->validate([
            'statement'     => 'required|string',
            'change_reason' => 'required|string',
            'title'         => 'nullable|string|max:255',
            'rationale'     => 'nullable|string',
        ]);

        $user = $request->user() ?? \App\Models\User::firstOrFail();

        $successor = $action->handle(
            oldPrinciple: $principle,
            actor: $user,
            newStatement: $validated['statement'],
            changeReason: $validated['change_reason'],
            newTitle: $validated['title'] ?? null,
            newRationale: $validated['rationale'] ?? null
        );

        return response()->json([
            'message'             => 'Principle superseded. Version chain preserved in graph.',
            'superseded_principle'=> $principle->fresh(),
            'new_principle'       => $successor,
        ], 201);
    }
}
