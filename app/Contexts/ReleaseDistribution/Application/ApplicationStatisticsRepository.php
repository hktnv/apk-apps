<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use App\Contexts\ReleaseDistribution\Domain\UpdateStatus;

interface ApplicationStatisticsRepository
{
    public function getForApplication(string $applicationId): ApplicationStatisticsView;

    public function recordUpdateCheck(string $applicationId, UpdateStatus $status): void;

    public function recordApkDownload(string $applicationId): void;
}
