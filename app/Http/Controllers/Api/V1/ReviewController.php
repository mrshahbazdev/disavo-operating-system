<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Review\Actions\CloseReview;
use App\Domains\Review\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = QueryBuilder::for(Review::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('period'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts(['id', 'review_date', 'created_at'])
            ->with(['items.module', 'improvements'])
            ->paginate(request()->integer('per_page', 25));

        return response()->json($reviews);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'period'      => 'required|string|max:30',
            'review_date' => 'required|date',
            'summary'     => 'nullable|string',
        ]);

        $review = Review::create([
            'title'       => $validated['title'],
            'period'      => $validated['period'],
            'review_date' => $validated['review_date'],
            'summary'     => $validated['summary'] ?? null,
            'status'      => Review::STATUS_OPEN,
            'created_by'  => $request->user()?->id ?? 1,
        ]);

        return response()->json($review, 201);
    }

    public function show(Review $review): JsonResponse
    {
        $review->load(['items.module', 'improvements', 'outgoingEdges', 'incomingEdges']);

        return response()->json($review);
    }

    public function close(
        Review $review,
        Request $request,
        CloseReview $action
    ): JsonResponse {
        $validated = $request->validate([
            'learning'           => 'nullable|array',
            'learning.title'     => 'required_with:learning|string',
            'learning.summary'   => 'required_with:learning|string',
            'learning.rationale' => 'nullable|string',
        ]);

        $user = $request->user() ?? \App\Models\User::firstOrFail();
        $closed = $action->handle($review, $user, $validated['learning'] ?? null);

        return response()->json([
            'message' => 'Review closed. Feedback loop closed to ALF.',
            'review'  => $closed,
        ]);
    }
}
