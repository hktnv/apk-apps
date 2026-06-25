<?php

declare(strict_types=1);

use App\Contexts\ApplicationCatalog\Presentation\Http\AgentApplicationApiController;
use App\Contexts\ReleaseDistribution\Presentation\Http\AgentReleaseApiController;
use App\Contexts\ReleaseDistribution\Presentation\Http\ArtifactDownloadController;
use App\Contexts\ReleaseDistribution\Presentation\Http\UpdateCheckController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/applications/{packageName}/channels/{channel}/update-check', UpdateCheckController::class)
        ->name('api.v1.update-check');

    Route::get('/artifacts/{releaseId}/download', ArtifactDownloadController::class)
        ->name('api.v1.artifacts.download');

    Route::middleware('agent.auth')->prefix('agent')->name('api.v1.agent.')->group(function (): void {
        Route::get('/applications', [AgentApplicationApiController::class, 'index'])->name('applications.index');
        Route::post('/applications', [AgentApplicationApiController::class, 'store'])->name('applications.store');
        Route::get('/applications/{applicationId}', [AgentApplicationApiController::class, 'show'])->name('applications.show');
        Route::post('/applications/{applicationId}/activate', [AgentApplicationApiController::class, 'activate'])->name('applications.activate');
        Route::post('/applications/{applicationId}/deactivate', [AgentApplicationApiController::class, 'deactivate'])->name('applications.deactivate');
        Route::post('/applications/{applicationId}/releases', [AgentReleaseApiController::class, 'store'])->name('releases.store');
        Route::post('/applications/{applicationId}/releases/{releaseId}/publish', [AgentReleaseApiController::class, 'publish'])->name('publications.publish');
        Route::post('/applications/{applicationId}/rollback', [AgentReleaseApiController::class, 'rollback'])->name('publications.rollback');
    });
});
