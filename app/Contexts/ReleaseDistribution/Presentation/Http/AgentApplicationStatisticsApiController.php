<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use App\Contexts\ApplicationCatalog\Application\GetApplicationDetails;
use App\Contexts\ReleaseDistribution\Application\ApplicationStatisticsRepository;
use App\Contexts\ReleaseDistribution\Application\ApplicationStatisticsView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AgentApplicationStatisticsApiController
{
    public function show(
        string $applicationId,
        Request $request,
        GetApplicationDetails $details,
        ApplicationStatisticsRepository $statistics,
    ): JsonResponse {
        if ($details->execute($applicationId) === null) {
            return response()->json([
                'error' => [
                    'code' => 'APPLICATION_NOT_FOUND',
                    'message' => 'Uygulama bulunamadı.',
                ],
                'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
            ], 404)->header('Cache-Control', 'no-store');
        }

        return response()->json([
            'data' => $this->statisticsData($statistics->getForApplication($applicationId)),
            'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
        ])->header('Cache-Control', 'no-store');
    }

    /** @return array<string, mixed> */
    private function statisticsData(ApplicationStatisticsView $statistics): array
    {
        return [
            'application_id' => $statistics->applicationId,
            'update_check_count' => $statistics->updateCheckCount,
            'update_available_count' => $statistics->updateAvailableCount,
            'up_to_date_count' => $statistics->upToDateCount,
            'apk_download_count' => $statistics->apkDownloadCount,
            'last_checked_at' => $statistics->lastCheckedAt,
            'last_downloaded_at' => $statistics->lastDownloadedAt,
        ];
    }
}
