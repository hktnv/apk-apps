<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use App\Contexts\ReleaseDistribution\Domain\UpdateDecision;

final readonly class CheckForUpdateResult
{
    public function __construct(
        public string $packageName,
        public string $channel,
        public int $currentVersionCode,
        public UpdateDecision $decision,
        public ?ReleaseView $release,
    ) {}
}
