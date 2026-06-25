<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

final readonly class PublicationView
{
    public function __construct(
        public string $id,
        public string $applicationId,
        public string $releaseId,
        public string $channel,
        public string $action,
        public bool $forceUpdate,
        public int $minimumSupportedVersionCode,
        public string $comment,
        public string $publishedByAdminId,
        public string $publishedAt,
        public int $releaseVersionCode,
    ) {}
}
