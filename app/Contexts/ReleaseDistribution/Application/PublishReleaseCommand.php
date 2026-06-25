<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

final readonly class PublishReleaseCommand
{
    public function __construct(
        public string $applicationId,
        public string $releaseId,
        public string $channel,
        public bool $forceUpdate,
        public int $minimumSupportedVersionCode,
        public string $comment,
        public string $publishedByAdminId,
    ) {}
}
