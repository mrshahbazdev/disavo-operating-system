<?php

use App\Http\Controllers\Api\V1\AuditController;
use App\Http\Controllers\Api\V1\GraphController;
use App\Http\Controllers\Api\V1\LearningController;
use App\Http\Controllers\Api\V1\ModuleController;
use App\Http\Controllers\Api\V1\ObservationController;
use App\Http\Controllers\Api\V1\PrincipleController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\QuickCaptureController;
use App\Http\Controllers\Api\V1\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Quick Capture (<60 seconds capture loop)
    Route::post('capture', [QuickCaptureController::class, 'store']);

    // ALF — Observations
    Route::get('observations', [ObservationController::class, 'index']);
    Route::post('observations', [ObservationController::class, 'store']);
    Route::get('observations/{observation}', [ObservationController::class, 'show']);

    // ALF — Learnings
    Route::get('learnings', [LearningController::class, 'index']);
    Route::post('learnings', [LearningController::class, 'store']);
    Route::get('learnings/{learning}', [LearningController::class, 'show']);
    Route::post('learnings/{learning}/validate', [LearningController::class, 'validateLearning']);
    Route::post('learnings/{learning}/promote', [LearningController::class, 'promote']);

    // ALF — Principles
    Route::get('principles', [PrincipleController::class, 'index']);
    Route::get('principles/{principle}', [PrincipleController::class, 'show']);
    Route::get('principles/{principle}/provenance', [PrincipleController::class, 'provenance']);
    Route::get('principles/{principle}/impact', [PrincipleController::class, 'impact']);
    Route::post('principles/{principle}/supersede', [PrincipleController::class, 'supersede']);

    // ALF — Questions (Offene Fragen)
    Route::get('questions', [QuestionController::class, 'index']);
    Route::post('questions', [QuestionController::class, 'store']);

    // AMF — Modules & Maturity Scoring
    Route::get('modules', [ModuleController::class, 'index']);
    Route::get('modules/{module}', [ModuleController::class, 'show']);
    Route::get('modules/{module}/maturity', [ModuleController::class, 'maturity']);
    Route::post('modules/{module}/audits/{template}/run', [AuditController::class, 'runAudit']);
    Route::post('audits/{auditRun}/score', [AuditController::class, 'scoreAudit']);

    // ARF — Reviews & Loop Closure
    Route::get('reviews', [ReviewController::class, 'index']);
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::get('reviews/{review}', [ReviewController::class, 'show']);
    Route::post('reviews/{review}/close', [ReviewController::class, 'close']);

    // Graph Engine (Neighborhood, Contradictions, Orphans, Edges, Invalidation)
    Route::get('graph/contradictions', [GraphController::class, 'contradictions']);
    Route::get('graph/orphans', [GraphController::class, 'orphans']);
    Route::get('graph/{type}/{id}', [GraphController::class, 'nodeGraph']);
    Route::post('edges', [GraphController::class, 'createEdge']);
    Route::delete('edges/{edge}', [GraphController::class, 'invalidateEdge']);

});
