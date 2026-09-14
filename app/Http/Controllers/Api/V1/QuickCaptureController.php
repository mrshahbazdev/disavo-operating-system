<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuickCaptureController extends Controller
{
    /**
     * Frictionless capture endpoint: log an Observation, Learning, or Question in under 5 seconds.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'    => 'required|in:observation,learning,question',
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'context' => 'nullable|array',
        ]);

        $userId = $request->user()?->id ?? 1;
        $content = $validated['content'] ?? $validated['title'];

        $model = match ($validated['type']) {
            'observation' => Observation::create([
                'title'      => $validated['title'],
                'content'    => $content,
                'context'    => $validated['context'] ?? null,
                'created_by' => $userId,
            ]),
            'learning'    => Learning::create([
                'title'      => $validated['title'],
                'summary'    => $content,
                'state'      => Draft::class,
                'created_by' => $userId,
            ]),
            'question'    => Question::create([
                'title'      => $validated['title'],
                'context'    => $content,
                'status'     => Question::STATUS_OPEN,
                'created_by' => $userId,
            ]),
        };

        return response()->json([
            'message' => "Successfully captured {$validated['type']} in DOS.",
            'type'    => $validated['type'],
            'node_id' => $model->id,
            'node'    => $model,
        ], 201);
    }
}
