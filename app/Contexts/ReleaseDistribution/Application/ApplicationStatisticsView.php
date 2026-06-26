<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

final readonly class ApplicationStatisticsView
{
    public function __construct(
        public string $applicationId,
        public int $updateCheckCount,
        public int $updateAvailableCount,
        public int $upToDateCount,
        public int $apkDownloadCount,
        public ?string $lastCheckedAt,
        public ?string $lastDownloadedAt,
    ) {}
}
