<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use App\Contexts\ReleaseDistribution\Application\PublicationView;
use App\Contexts\ReleaseDistribution\Application\PublishRelease;
use App\Contexts\ReleaseDistribution\Application\PublishReleaseCommand;
use App\Contexts\ReleaseDistribution\Application\ReleaseView;
use App\Contexts\ReleaseDistribution\Application\UploadRelease;
use App\Contexts\ReleaseDistribution\Application\UploadReleaseCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AgentReleaseApiController
{
    public function store(string $applicationId, Request $request, UploadRelease $uploadRelease): JsonResponse
    {
        $data = $request->validate([
            'version_code' => ['required', 'integer', 'min:1'],
            'version_name' => ['required', 'string', 'max:255'],
            'release_notes' => ['required', 'string'],
            'apk' => ['required', 'file', 'max:'.((int) config('apk.max_upload_mb') * 1024)],
        ]);

        $file = $request->file('apk');
        if ($file === null) {
            return $this->operationError($request, 'APK_REQUIRED', 'APK dosyası gerekli.');
        }

        $result = $uploadRelease->execute(new UploadReleaseCommand(
            $applicationId,
            (int) $data['version_code'],
            $data['version_name'],
            $data['release_notes'],
            (string) $file->getClientOriginalName(),
            (string) $file->getRealPath(),
            (string) $request->attributes->get('agent_admin_actor_id'),
        ));

        if (! $result->ok) {
            return $this->operationError($request, $result->errorCode ?? 'RELEASE_UPLOAD_FAILED', $result->message ?? 'Release yüklenemedi.');
        }

        /** @var ReleaseView $release */
        $release = $result->value;

        return $this->ok($request, $this->releaseData($release), 201);
    }

    public function publish(
        string $applicationId,
        string $releaseId,
        Request $request,
        PublishRelease $publishRelease,
    ): JsonResponse {
        return $this->writePublication($applicationId, $releaseId, $request, $publishRelease, false);
    }

    public function rollback(string $applicationId, Request $request, PublishRelease $publishRelease): JsonResponse
    {
        $data = $request->validate([
            'release_id' => ['required', 'string'],
        ]);

        return $this->writePublication($applicationId, $data['release_id'], $request, $publishRelease, true);
    }

    private function writePublication(
        string $applicationId,
        string $releaseId,
        Request $request,
        PublishRelease $publishRelease,
        bool $rollback,
    ): JsonResponse {
        $data = $request->validate([
            'channel' => ['required', 'in:stable,beta,internal'],
            'force_update' => ['nullable', 'boolean'],
            'minimum_supported_version_code' => ['required', 'integer', 'min:0'],
            'comment' => ['required', 'string', 'min:3'],
        ]);

        $command = new PublishReleaseCommand(
            $applicationId,
            $releaseId,
            $data['channel'],
            $request->boolean('force_update'),
            (int) $data['minimum_supported_version_code'],
            $data['comment'],
            (string) $request->attributes->get('agent_admin_actor_id'),
        );

        $result = $rollback ? $publishRelease->rollback($command) : $publishRelease->execute($command);
        if (! $result->ok) {
            return $this->operationError($request, $result->errorCode ?? 'PUBLICATION_FAILED', $result->message ?? 'Yayın işlemi tamamlanamadı.');
        }

        /** @var PublicationView $publication */
        $publication = $result->value;

        return $this->ok($request, [
            'id' => $publication->id,
            'application_id' => $publication->applicationId,
            'release_id' => $publication->releaseId,
            'channel' => $publication->channel,
            'action' => $publication->action,
            'force_update' => $publication->forceUpdate,
            'minimum_supported_version_code' => $publication->minimumSupportedVersionCode,
            'comment' => $publication->comment,
            'published_at' => $publication->publishedAt,
        ], 201);
    }

    /** @return array<string, mixed> */
    private function releaseData(ReleaseView $release): array
    {
        return [
            'id' => $release->id,
            'application_id' => $release->applicationId,
            'version_code' => $release->versionCode,
            'version_name' => $release->versionName,
            'release_notes' => $release->releaseNotes,
            'original_filename' => $release->originalFilename,
            'sha256' => $release->sha256,
            'size_bytes' => $release->sizeBytes,
        ];
    }

    private function ok(Request $request, mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
        ], $status)->header('Cache-Control', 'no-store');
    }

    private function operationError(Request $request, string $code, string $message, int $status = 422): JsonResponse
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
