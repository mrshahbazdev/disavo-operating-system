<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domains\Development\Models\Module;
use App\Domains\Development\Services\MaturityScoringService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(): JsonResponse
    {
        // Return top-level modules with nested children
        $modules = Module::with(['children', 'goals', 'kpis'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return response()->json($modules);
    }

    public function show(Module $module, MaturityScoringService $scoringService): JsonResponse
    {
        $module->load([
            'children',
            'goals',
            'kpis.readings',
            'tools',
            'auditTemplates.questions',
            'auditRuns',
        ]);

        $maturity = $scoringService->getMaturityDelta($module);

        return response()->json([
            'module'   => $module,
            'maturity' => $maturity,
        ]);
    }

    public function maturity(Module $module, MaturityScoringService $scoringService): JsonResponse
    {
        $delta = $scoringService->getMaturityDelta($module);

        return response()->json($delta);
    }
}
