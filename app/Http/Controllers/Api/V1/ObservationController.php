<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Knowledge\Models\Observation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ObservationController extends Controller
{
    public function index(): JsonResponse
    {
        $observations = QueryBuilder::for(Observation::class)
            ->allowedFilters(['title'])
            ->allowedSorts(['id', 'created_at'])
            ->with(['creator'])
            ->paginate(request()->integer('per_page', 25));

        return response()->json($observations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'context' => 'nullable|array',
        ]);

        $observation = Observation::create([
            'title'      => $validated['title'],
            'content'    => $validated['content'],
            'context'    => $validated['context'] ?? null,
            'created_by' => $request->user()?->id ?? 1,
        ]);

        return response()->json($observation, 201);
    }

    public function show(Observation $observation): JsonResponse
    {
        $observation->load(['creator', 'outgoingEdges', 'incomingEdges']);

        return response()->json($observation);
    }
}
