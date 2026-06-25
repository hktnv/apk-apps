<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use App\SharedKernel\Domain\OperationResult;

final class ResolveArtifactDownload
{
    public function __construct(
        private readonly ReleaseRepository $releases,
        private readonly PublicationRepository $publications,
        private readonly ArtifactStorage $storage,
    ) {}

    public function execute(string $releaseId): OperationResult
    {
        $release = $this->releases->find($releaseId);
        if ($release === null || ! $this->publications->isReleasePublished($releaseId)) {
            return OperationResult::failure('ARTIFACT_NOT_FOUND', 'Artifact bulunamadı.');
        }

        if (! $this->storage->exists($release->storagePath)) {
            return OperationResult::failure('PHYSICAL_ARTIFACT_MISSING', 'Artifact dosyası storage içinde bulunamadı.');
        }

        return OperationResult::success($release);
    }
}
