<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

use Illuminate\Support\Collection;

interface ManagedApplicationRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): ManagedApplicationView;

    public function find(string $id): ?ManagedApplicationView;

    public function findActiveByPackageName(string $packageName): ?ManagedApplicationView;

    public function existsBySlug(string $slug, ?string $exceptId = null): bool;

    public function existsByPackageName(string $packageName): bool;

    /** @param array<string, mixed> $attributes */
    public function update(string $id, array $attributes): bool;

    /** @return Collection<int, ManagedApplicationView> */
    public function all(): Collection;
}
