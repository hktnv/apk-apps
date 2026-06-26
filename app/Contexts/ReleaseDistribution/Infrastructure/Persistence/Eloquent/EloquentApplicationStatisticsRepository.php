<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent;

use App\Contexts\ReleaseDistribution\Application\ApplicationStatisticsRepository;
use App\Contexts\ReleaseDistribution\Application\ApplicationStatisticsView;
use App\Contexts\ReleaseDistribution\Domain\UpdateStatus;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

final class EloquentApplicationStatisticsRepository implements ApplicationStatisticsRepository
{
    public function getForApplication(string $applicationId): ApplicationStatisticsView
    {
        $record = ApplicationStatisticsRecord::query()->find($applicationId);

        return $record instanceof ApplicationStatisticsRecord
            ? $this->map($record)
            : new ApplicationStatisticsView($applicationId, 0, 0, 0, 0, null, null);
    }

    public function recordUpdateCheck(string $applicationId, UpdateStatus $status): void
    {
        $this->ensureRecord($applicationId);

        $updates = [
            'update_check_count' => DB::raw('update_check_count + 1'),
            'last_checked_at' => now(),
        ];

        if ($status === UpdateStatus::UpdateAvailable) {
            $updates['update_available_count'] = DB::raw('update_available_count + 1');
        }

        if ($status === UpdateStatus::UpToDate) {
            $updates['up_to_date_count'] = DB::raw('up_to_date_count + 1');
        }

        ApplicationStatisticsRecord::query()
            ->whereKey($applicationId)
            ->update($updates);
    }

    public function recordApkDownload(string $applicationId): void
    {
        $this->ensureRecord($applicationId);

        ApplicationStatisticsRecord::query()
            ->whereKey($applicationId)
            ->update([
                'apk_download_count' => DB::raw('apk_download_count + 1'),
                'last_downloaded_at' => now(),
            ]);
    }

    private function ensureRecord(string $applicationId): void
    {
        ApplicationStatisticsRecord::query()->firstOrCreate(
            ['application_id' => $applicationId],
            [
                'update_check_count' => 0,
                'update_available_count' => 0,
                'up_to_date_count' => 0,
                'apk_download_count' => 0,
            ],
        );
    }

    private function map(ApplicationStatisticsRecord $record): ApplicationStatisticsView
    {
        return new ApplicationStatisticsView(
            (string) $record->getAttribute('application_id'),
            (int) $record->getAttribute('update_check_count'),
            (int) $record->getAttribute('update_available_count'),
            (int) $record->getAttribute('up_to_date_count'),
            (int) $record->getAttribute('apk_download_count'),
            $this->dateToIsoString($record->getAttribute('last_checked_at')),
            $this->dateToIsoString($record->getAttribute('last_downloaded_at')),
        );
    }

    private function dateToIsoString(mixed $value): ?string
    {
        return $value instanceof DateTimeInterface ? $value->format(DateTimeInterface::ATOM) : null;
    }
}
