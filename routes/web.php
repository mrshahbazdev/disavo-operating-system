<?php

use App\Domains\Development\Models\Module;

use App\Domains\Graph\Models\KnowledgeEdge;
use App\Domains\Knowledge\Models\Principle;
use App\Domains\Review\Models\Review;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Public Welcome / Portal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Executive Dashboard
Route::middleware('auth')->get('dashboard', function () {
    $tenant = TenantContext::getTenant() ?? Tenant::first();

    $principles = rescue(fn () => \App\Domains\Knowledge\Models\Principle::withoutGlobalScopes()->get(), collect());
    $learnings = rescue(fn () => \App\Domains\Knowledge\Models\Learning::withoutGlobalScopes()->get(), collect());
    $observations = rescue(fn () => \App\Domains\Knowledge\Models\Observation::withoutGlobalScopes()->get(), collect());
    $reviews = rescue(fn () => \App\Domains\Review\Models\Review::withoutGlobalScopes()->with(['items.module', 'improvements'])->get(), collect());
    $auditTemplates = rescue(fn () => \App\Domains\Development\Models\AuditTemplate::withoutGlobalScopes()->with('questions')->get(), collect());
    $edges = rescue(fn () => KnowledgeEdge::withoutGlobalScopes()->active()->get(), collect());

    $modules = rescue(function () use ($principles, $edges) {
        return Module::withoutGlobalScopes()
            ->with(['goals', 'kpis', 'tools', 'auditRuns.template'])
            ->get()
            ->map(function ($module) use ($principles, $edges) {
                $governingPrincipleIds = $edges
                    ->where('target_type', 'module')
                    ->where('target_id', $module->id)
                    ->where('relation', 'governs')
                    ->pluck('source_id')
                    ->all();

                $module->governing_principles = $principles->whereIn('id', $governingPrincipleIds)->values();
                return $module;
            });
    }, collect());

    $principlesCount = $principles->count();
    $edgesCount = $edges->count();
    $reviewsCount = $reviews->count();

    return view('dashboard', compact(
        'tenant',
        'modules',
        'principles',
        'learnings',
        'observations',
        'reviews',
        'auditTemplates',
        'principlesCount',
        'edgesCount',
        'reviewsCount'
    ));
})->name('dashboard');




