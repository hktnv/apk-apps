<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use App\Contexts\ReleaseDistribution\Application\ArtifactStorage;
use App\Contexts\ReleaseDistribution\Application\ReleaseView;
use App\Contexts\ReleaseDistribution\Application\ResolveArtifactDownload;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ArtifactDownloadController
{
    public function __invoke(
        string $releaseId,
        Request $request,
        ResolveArtifactDownload $resolve,
        ArtifactStorage $storage,
    ): BinaryFileResponse|JsonResponse {
        $result = $resolve->execute($releaseId);

        if (! $result->ok) {
            if ($result->errorCode === 'PHYSICAL_ARTIFACT_MISSING') {
                return response()->json([
                    'error' => [
                        'code' => 'ARTIFACT_UNAVAILABLE',
                        'message' => 'Artifact geçici olarak indirilemiyor.',
                    ],
                    'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
                ], 500);
            }

            return response()->json([
                'error' => [
                    'code' => 'ARTIFACT_NOT_FOUND',
                    'message' => 'Artifact bulunamadı.',
                ],
                'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
            ], 404);
        }

        /** @var ReleaseView $release */
        $release = $result->value;

        return response()->download(
            $storage->absolutePath($release->storagePath),
            'application-'.$release->versionCode.'.apk',
            [
                'Content-Type' => 'application/vnd.android.package-archive',
                'X-APK-SHA256' => $release->sha256,
                'X-APK-Size' => (string) $release->sizeBytes,
            ],
        );
    }
}
