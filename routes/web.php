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
    $modules = rescue(fn () => Module::withoutGlobalScopes()->get(), collect());
    $principlesCount = rescue(fn () => Principle::withoutGlobalScopes()->count(), 0);
    $edgesCount = rescue(fn () => KnowledgeEdge::withoutGlobalScopes()->active()->count(), 0);
    $reviewsCount = rescue(fn () => Review::withoutGlobalScopes()->count(), 0);

    return view('dashboard', compact(
        'tenant',
        'modules',
        'principlesCount',
        'edgesCount',
        'reviewsCount'
    ));
})->name('dashboard');



