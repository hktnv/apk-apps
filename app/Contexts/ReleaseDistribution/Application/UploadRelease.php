<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use App\Contexts\ApplicationCatalog\Application\ManagedApplicationRepository;
use App\SharedKernel\Domain\IdentifierGenerator;
use App\SharedKernel\Domain\OperationResult;
use App\SharedKernel\Domain\TransactionRunner;
use Throwable;

final class UploadRelease
{
    public function __construct(
        private readonly ManagedApplicationRepository $applications,
        private readonly ReleaseRepository $releases,
        private readonly ApkFileValidator $validator,
        private readonly ArtifactStorage $storage,
        private readonly IdentifierGenerator $ids,
        private readonly TransactionRunner $transactions,
    ) {}

    public function execute(UploadReleaseCommand $command): OperationResult
    {
        $application = $this->applications->find($command->applicationId);
        if ($application === null) {
            return OperationResult::failure('APPLICATION_NOT_FOUND', 'Uygulama bulunamadı.');
        }

        $currentMax = $this->releases->maxVersionCodeForApplication($command->applicationId);
        if ($currentMax !== null && $command->versionCode <= $currentMax) {
            return OperationResult::failure('VERSION_CODE_NOT_GREATER', 'Version code mevcut en yüksek değerden büyük olmalıdır.');
        }

        $maxBytes = (int) config('apk.max_upload_mb') * 1024 * 1024;
        $validation = $this->validator->validate($command->localFilePath, $command->originalFilename, $maxBytes);
        if (! $validation->ok) {
            return $validation;
        }

        $releaseId = $this->ids->newUlid();
        $storagePath = $command->applicationId.'/'.$releaseId.'/application.apk';
        $hash = hash_file('sha256', $command->localFilePath);
        $size = filesize($command->localFilePath);

        if ($hash === false || $size === false) {
            return OperationResult::failure('APK_READ_FAILED', 'APK dosyası okunamadı.');
        }

        $this->storage->storeFromLocalFile($command->localFilePath, $storagePath);

        try {
            $release = $this->transactions->run(function () use ($command, $releaseId, $storagePath, $hash, $size): ReleaseView {
                return $this->releases->create([
                    'id' => $releaseId,
                    'application_id' => $command->applicationId,
                    'version_code' => $command->versionCode,
                    'version_name' => $command->versionName,
                    'release_notes' => $command->releaseNotes,
                    'original_filename' => $command->originalFilename,
                    'storage_disk' => (string) config('apk.storage_disk'),
                    'storage_path' => $storagePath,
                    'sha256' => $hash,
                    'size_bytes' => (int) $size,
                    'uploaded_by_admin_id' => $command->uploadedByAdminId,
                ]);
            });
        } catch (Throwable $throwable) {
            $this->storage->delete($storagePath);

            throw $throwable;
        }

        return OperationResult::success($release);
    }
}
