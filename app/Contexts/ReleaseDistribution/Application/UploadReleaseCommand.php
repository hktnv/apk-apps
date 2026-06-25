<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

final readonly class UploadReleaseCommand
{
    public function __construct(
        public string $applicationId,
        public int $versionCode,
        public string $versionName,
        public string $releaseNotes,
        public string $originalFilename,
        public string $localFilePath,
        public string $uploadedByAdminId,
    ) {}
}
