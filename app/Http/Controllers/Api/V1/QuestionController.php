<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Knowledge\Models\Question;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuestionController extends Controller
{
    public function index(): JsonResponse
    {
        $questions = QueryBuilder::for(Question::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts(['id', 'created_at'])
            ->with(['creator', 'answeredByUser'])
            ->paginate(request()->integer('per_page', 25));

        return response()->json($questions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'context' => 'nullable|string',
        ]);

        $question = Question::create([
            'title'      => $validated['title'],
            'context'    => $validated['context'] ?? null,
            'status'     => Question::STATUS_OPEN,
            'created_by' => $request->user()?->id ?? 1,
        ]);

        return response()->json($question, 201);
    }
}
