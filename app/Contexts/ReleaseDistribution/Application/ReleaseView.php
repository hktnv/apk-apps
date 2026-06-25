<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

final readonly class ReleaseView
{
    public function __construct(
        public string $id,
        public string $applicationId,
        public int $versionCode,
        public string $versionName,
        public string $releaseNotes,
        public string $originalFilename,
        public string $storageDisk,
        public string $storagePath,
        public string $sha256,
        public int $sizeBytes,
        public string $uploadedByAdminId,
    ) {}
}
