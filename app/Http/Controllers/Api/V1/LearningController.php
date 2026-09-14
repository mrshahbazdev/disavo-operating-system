<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Knowledge\Actions\PromoteLearningToPrinciple;
use App\Domains\Knowledge\Actions\ValidateLearning;
use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class LearningController extends Controller
{
    public function index(): JsonResponse
    {
        $learnings = QueryBuilder::for(Learning::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('state'),
                AllowedFilter::scope('active'),
            ])
            ->allowedSorts(['id', 'created_at', 'title'])
            ->with(['creator', 'validator', 'promoter'])
            ->paginate(request()->integer('per_page', 25));

        return response()->json($learnings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'summary'   => 'required|string',
            'rationale' => 'nullable|string',
        ]);

        $learning = Learning::create([
            'title'      => $validated['title'],
            'summary'    => $validated['summary'],
            'rationale'  => $validated['rationale'] ?? null,
            'state'      => Draft::class,
            'created_by' => $request->user()?->id ?? 1,
        ]);

        return response()->json($learning, 201);
    }

    public function show(Learning $learning): JsonResponse
    {
        $learning->load(['creator', 'validator', 'promoter', 'outgoingEdges', 'incomingEdges']);

        return response()->json($learning);
    }

    public function validateLearning(
        Learning $learning,
        Request $request,
        ValidateLearning $action
    ): JsonResponse {
        $user = $request->user() ?? \App\Models\User::firstOrFail();
        $updated = $action->handle($learning, $user);

        return response()->json([
            'message'  => 'Learning successfully validated.',
            'learning' => $updated,
        ]);
    }

    public function promote(
        Learning $learning,
        Request $request,
        PromoteLearningToPrinciple $action
    ): JsonResponse {
        $validated = $request->validate([
            'statement'       => 'required|string',
            'principle_title' => 'nullable|string|max:255',
            'rationale'       => 'nullable|string',
        ]);

        $user = $request->user() ?? \App\Models\User::firstOrFail();

        $principle = $action->handle(
            learning: $learning,
            steward: $user,
            statement: $validated['statement'],
            principleTitle: $validated['principle_title'] ?? null,
            rationale: $validated['rationale'] ?? null
        );

        return response()->json([
            'message'   => 'Learning promoted to active Principle with graph edge.',
            'principle' => $principle,
            'learning'  => $learning->fresh(),
        ], 201);
    }
}
