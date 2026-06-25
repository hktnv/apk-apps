<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use App\Contexts\ReleaseDistribution\Application\CheckForUpdate;
use App\Contexts\ReleaseDistribution\Application\CheckForUpdateResult;
use App\Contexts\ReleaseDistribution\Domain\UpdateStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UpdateCheckController
{
    public function __invoke(
        string $packageName,
        string $channel,
        Request $request,
        CheckForUpdate $checkForUpdate,
    ): JsonResponse {
        $validated = $request->validate([
            'current_version_code' => ['required', 'integer', 'min:0'],
        ]);

        $result = $checkForUpdate->execute($packageName, $channel, (int) $validated['current_version_code']);
        if (! $result->ok) {
            $status = $result->errorCode === 'APPLICATION_NOT_FOUND' ? 404 : 422;

            return $this->error($result->errorCode ?? 'INVALID_REQUEST', $result->message ?? 'İstek geçerli değil.', $request, $status);
        }

        /** @var CheckForUpdateResult $payload */
        $payload = $result->value;
        $data = [
            'status' => $payload->decision->status->value,
            'package_name' => $payload->packageName,
            'channel' => $payload->channel,
            'current_version_code' => $payload->currentVersionCode,
        ];

        if ($payload->decision->publishedVersionCode !== null) {
            $data['published_version_code'] = $payload->decision->publishedVersionCode;
        }

        if ($payload->decision->status === UpdateStatus::UpdateAvailable && $payload->release !== null) {
            $data['required'] = $payload->decision->required;
            $data['release'] = [
                'id' => $payload->release->id,
                'version_code' => $payload->release->versionCode,
                'version_name' => $payload->release->versionName,
                'release_notes' => $payload->release->releaseNotes,
                'sha256' => $payload->release->sha256,
                'size_bytes' => $payload->release->sizeBytes,
                'download_url' => route('api.v1.artifacts.download', ['releaseId' => $payload->release->id], true),
            ];
        }

        return response()->json([
            'data' => $data,
            'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
        ])->header('Cache-Control', 'no-store');
    }

    private function error(string $code, string $message, Request $request, int $status): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
        ], $status)->header('Cache-Control', 'no-store');
    }
}
