<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use Illuminate\Support\Collection;

interface PublicationRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): PublicationView;

    public function current(string $applicationId, string $channel): ?PublicationView;

    public function isReleasePublished(string $releaseId): bool;

    /** @return Collection<int, PublicationView> */
    public function historyForApplication(string $applicationId): Collection;

    /** @return Collection<int, PublicationView> */
    public function currentByChannel(): Collection;
}
