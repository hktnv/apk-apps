<?php

declare(strict_types=1);

use App\Contexts\ReleaseDistribution\Presentation\Http\ArtifactDownloadController;
use App\Contexts\ReleaseDistribution\Presentation\Http\UpdateCheckController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/applications/{packageName}/channels/{channel}/update-check', UpdateCheckController::class)
        ->name('api.v1.update-check');

    Route::get('/artifacts/{releaseId}/download', ArtifactDownloadController::class)
        ->name('api.v1.artifacts.download');
});
