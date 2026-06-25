<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Domain;

final class UpdateDecisionPolicy
{
    public function decide(
        int $currentVersionCode,
        ?int $publishedVersionCode,
        bool $forceUpdate,
        int $minimumSupportedVersionCode,
    ): UpdateDecision {
        if ($publishedVersionCode === null) {
            return new UpdateDecision(UpdateStatus::NoPublishedRelease, null, null);
        }

        if ($currentVersionCode < $publishedVersionCode) {
            return new UpdateDecision(
                UpdateStatus::UpdateAvailable,
                $publishedVersionCode,
                $forceUpdate || $currentVersionCode < $minimumSupportedVersionCode,
            );
        }

        if ($currentVersionCode === $publishedVersionCode) {
            return new UpdateDecision(UpdateStatus::UpToDate, $publishedVersionCode, null);
        }

        return new UpdateDecision(UpdateStatus::ClientAhead, $publishedVersionCode, null);
    }
}
