<?php

declare(strict_types=1);

use App\Contexts\ApplicationCatalog\Presentation\Http\ApplicationController;
use App\Contexts\IdentityAccess\Presentation\Http\AgentController;
use App\Contexts\IdentityAccess\Presentation\Http\LoginController;
use App\Contexts\ReleaseDistribution\Presentation\Http\AdminReleaseController;
use App\Contexts\ReleaseDistribution\Presentation\Http\DashboardController;
use App\Contexts\ReleaseDistribution\Presentation\Http\HealthController;
use App\Contexts\ReleaseDistribution\Presentation\Http\PublicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

Route::get('/health/live', [HealthController::class, 'live'])->name('health.live');
Route::get('/health/ready', [HealthController::class, 'ready'])->name('health.ready');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
    Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
    Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    Route::post('/agents/{agentId}/activate', [AgentController::class, 'activate'])->name('agents.activate');
    Route::post('/agents/{agentId}/deactivate', [AgentController::class, 'deactivate'])->name('agents.deactivate');
    Route::post('/agents/{agentId}/rotate-secret', [AgentController::class, 'rotateSecret'])->name('agents.rotate-secret');

    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/{applicationId}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/applications/{applicationId}/edit', [ApplicationController::class, 'edit'])->name('applications.edit');
    Route::put('/applications/{applicationId}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::post('/applications/{applicationId}/activate', [ApplicationController::class, 'activate'])->name('applications.activate');
    Route::post('/applications/{applicationId}/deactivate', [ApplicationController::class, 'deactivate'])->name('applications.deactivate');

    Route::get('/applications/{applicationId}/releases', [AdminReleaseController::class, 'index'])->name('releases.index');
    Route::get('/applications/{applicationId}/releases/create', [AdminReleaseController::class, 'create'])->name('releases.create');
    Route::post('/applications/{applicationId}/releases', [AdminReleaseController::class, 'store'])->name('releases.store');
    Route::get('/applications/{applicationId}/releases/{releaseId}', [AdminReleaseController::class, 'show'])->name('releases.show');

    Route::get('/applications/{applicationId}/releases/{releaseId}/publish', [PublicationController::class, 'publishForm'])->name('publications.publish-form');
    Route::post('/applications/{applicationId}/releases/{releaseId}/publish', [PublicationController::class, 'publish'])->name('publications.publish');
    Route::get('/applications/{applicationId}/rollback', [PublicationController::class, 'rollbackForm'])->name('publications.rollback-form');
    Route::post('/applications/{applicationId}/rollback', [PublicationController::class, 'rollback'])->name('publications.rollback');
});
