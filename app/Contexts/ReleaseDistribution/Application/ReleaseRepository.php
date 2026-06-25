<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use Illuminate\Support\Collection;

interface ReleaseRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): ReleaseView;

    public function find(string $id): ?ReleaseView;

    public function maxVersionCodeForApplication(string $applicationId): ?int;

    /** @return Collection<int, ReleaseView> */
    public function listForApplication(string $applicationId): Collection;

    public function count(): int;

    /** @return Collection<int, ReleaseView> */
    public function latest(int $limit): Collection;
}
