<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Infrastructure\Persistence\Eloquent;

use App\Contexts\ApplicationCatalog\Application\ManagedApplicationRepository;
use App\Contexts\ApplicationCatalog\Application\ManagedApplicationView;
use Illuminate\Support\Collection;

final class EloquentManagedApplicationRepository implements ManagedApplicationRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): ManagedApplicationView
    {
        return $this->map(ManagedApplicationRecord::query()->create($attributes));
    }

    public function find(string $id): ?ManagedApplicationView
    {
        $record = ManagedApplicationRecord::query()->find($id);

        return $record instanceof ManagedApplicationRecord ? $this->map($record) : null;
    }

    public function findActiveByPackageName(string $packageName): ?ManagedApplicationView
    {
        $record = ManagedApplicationRecord::query()
            ->where('package_name', $packageName)
            ->where('is_active', true)
            ->first();

        return $record instanceof ManagedApplicationRecord ? $this->map($record) : null;
    }

    /** @param array<string, mixed> $attributes */
    public function update(string $id, array $attributes): bool
    {
        return ManagedApplicationRecord::query()->whereKey($id)->update($attributes) === 1;
    }

    public function existsBySlug(string $slug, ?string $exceptId = null): bool
    {
        $query = ManagedApplicationRecord::query()->where('slug', $slug);

        if ($exceptId !== null) {
            $query->whereKeyNot($exceptId);
        }

        return $query->exists();
    }

    public function existsByPackageName(string $packageName): bool
    {
        return ManagedApplicationRecord::query()->where('package_name', $packageName)->exists();
    }

    /** @return Collection<int, ManagedApplicationView> */
    public function all(): Collection
    {
        return ManagedApplicationRecord::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (ManagedApplicationRecord $record): ManagedApplicationView => $this->map($record));
    }

    private function map(ManagedApplicationRecord $record): ManagedApplicationView
    {
        return new ManagedApplicationView(
            (string) $record->getAttribute('id'),
            (string) $record->getAttribute('name'),
            (string) $record->getAttribute('slug'),
            (string) $record->getAttribute('package_name'),
            $record->getAttribute('description') === null ? null : (string) $record->getAttribute('description'),
            (bool) $record->getAttribute('is_active'),
        );
    }
}
