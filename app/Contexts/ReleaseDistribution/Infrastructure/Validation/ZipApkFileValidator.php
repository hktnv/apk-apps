<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Validation;

use App\Contexts\ReleaseDistribution\Application\ApkFileValidator;
use App\SharedKernel\Domain\OperationResult;
use ZipArchive;

final class ZipApkFileValidator implements ApkFileValidator
{
    public function validate(string $localPath, string $originalFilename, int $maxBytes): OperationResult
    {
        if (! str_ends_with(strtolower($originalFilename), '.apk')) {
            return OperationResult::failure('INVALID_APK_EXTENSION', 'Dosya APK olarak yüklenmelidir.');
        }

        $size = filesize($localPath);
        if ($size === false || $size <= 0 || $size > $maxBytes) {
            return OperationResult::failure('INVALID_APK_SIZE', 'APK dosya boyutu izin verilen aralıkta değil.');
        }

        $header = file_get_contents($localPath, false, null, 0, 4);
        if ($header !== "PK\x03\x04") {
            return OperationResult::failure('INVALID_APK_ZIP_HEADER', 'APK geçerli bir ZIP arşivi değil.');
        }

        $zip = new ZipArchive;
        if ($zip->open($localPath) !== true) {
            return OperationResult::failure('INVALID_APK_ZIP', 'APK arşivi okunamadı.');
        }

        try {
            if ($zip->locateName('AndroidManifest.xml') === false) {
                return OperationResult::failure('MISSING_ANDROID_MANIFEST', 'APK içinde AndroidManifest.xml bulunamadı.');
            }
        } finally {
            $zip->close();
        }

        return OperationResult::success();
    }
}
