<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent;

use App\Contexts\ReleaseDistribution\Application\PublicationRepository;
use App\Contexts\ReleaseDistribution\Application\PublicationView;
use Illuminate\Support\Collection;

final class EloquentPublicationRepository implements PublicationRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): PublicationView
    {
        $record = ReleasePublicationRecord::query()->create($attributes);

        return $this->map($record);
    }

    public function current(string $applicationId, string $channel): ?PublicationView
    {
        $record = ReleasePublicationRecord::query()
            ->select('release_publications.*', 'apk_releases.version_code as release_version_code')
            ->join('apk_releases', 'apk_releases.id', '=', 'release_publications.release_id')
            ->where('release_publications.application_id', $applicationId)
            ->where('release_publications.channel', $channel)
            ->orderByDesc('release_publications.id')
            ->first();

        return $record instanceof ReleasePublicationRecord ? $this->map($record) : null;
    }

    public function isReleasePublished(string $releaseId): bool
    {
        return ReleasePublicationRecord::query()->where('release_id', $releaseId)->exists();
    }

    /** @return Collection<int, PublicationView> */
    public function historyForApplication(string $applicationId): Collection
    {
        return ReleasePublicationRecord::query()
            ->select('release_publications.*', 'apk_releases.version_code as release_version_code')
            ->join('apk_releases', 'apk_releases.id', '=', 'release_publications.release_id')
            ->where('release_publications.application_id', $applicationId)
            ->orderByDesc('release_publications.id')
            ->get()
            ->map(fn (ReleasePublicationRecord $record): PublicationView => $this->map($record));
    }

    /** @return Collection<int, PublicationView> */
    public function currentByChannel(): Collection
    {
        return ReleasePublicationRecord::query()
            ->select('release_publications.*', 'apk_releases.version_code as release_version_code')
            ->join('apk_releases', 'apk_releases.id', '=', 'release_publications.release_id')
            ->orderByDesc('release_publications.id')
            ->get()
            ->unique(fn (ReleasePublicationRecord $record): string => $record->getAttribute('application_id').':'.$record->getAttribute('channel'))
            ->values()
            ->map(fn (ReleasePublicationRecord $record): PublicationView => $this->map($record));
    }

    private function map(ReleasePublicationRecord $record): PublicationView
    {
        return new PublicationView(
            (string) $record->getAttribute('id'),
            (string) $record->getAttribute('application_id'),
            (string) $record->getAttribute('release_id'),
            (string) $record->getAttribute('channel'),
            (string) $record->getAttribute('action'),
            (bool) $record->getAttribute('force_update'),
            (int) $record->getAttribute('minimum_supported_version_code'),
            (string) $record->getAttribute('comment'),
            (string) $record->getAttribute('published_by_admin_id'),
            (string) $record->getAttribute('published_at'),
            (int) ($record->getAttribute('release_version_code') ?? 0),
        );
    }
}
