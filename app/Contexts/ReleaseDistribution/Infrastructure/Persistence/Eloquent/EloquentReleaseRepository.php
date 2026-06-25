<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent;

use App\Contexts\ReleaseDistribution\Application\ReleaseRepository;
use App\Contexts\ReleaseDistribution\Application\ReleaseView;
use Illuminate\Support\Collection;

final class EloquentReleaseRepository implements ReleaseRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): ReleaseView
    {
        return $this->map(ApkReleaseRecord::query()->create($attributes));
    }

    public function find(string $id): ?ReleaseView
    {
        $record = ApkReleaseRecord::query()->find($id);

        return $record instanceof ApkReleaseRecord ? $this->map($record) : null;
    }

    public function maxVersionCodeForApplication(string $applicationId): ?int
    {
        $value = ApkReleaseRecord::query()
            ->where('application_id', $applicationId)
            ->max('version_code');

        return $value === null ? null : (int) $value;
    }

    /** @return Collection<int, ReleaseView> */
    public function listForApplication(string $applicationId): Collection
    {
        return ApkReleaseRecord::query()
            ->where('application_id', $applicationId)
            ->orderByDesc('version_code')
            ->get()
            ->map(fn (ApkReleaseRecord $record): ReleaseView => $this->map($record));
    }

    public function count(): int
    {
        return ApkReleaseRecord::query()->count();
    }

    /** @return Collection<int, ReleaseView> */
    public function latest(int $limit): Collection
    {
        return ApkReleaseRecord::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (ApkReleaseRecord $record): ReleaseView => $this->map($record));
    }

    private function map(ApkReleaseRecord $record): ReleaseView
    {
        return new ReleaseView(
            (string) $record->getAttribute('id'),
            (string) $record->getAttribute('application_id'),
            (int) $record->getAttribute('version_code'),
            (string) $record->getAttribute('version_name'),
            (string) $record->getAttribute('release_notes'),
            (string) $record->getAttribute('original_filename'),
            (string) $record->getAttribute('storage_disk'),
            (string) $record->getAttribute('storage_path'),
            (string) $record->getAttribute('sha256'),
            (int) $record->getAttribute('size_bytes'),
            (string) $record->getAttribute('uploaded_by_admin_id'),
        );
    }
}
