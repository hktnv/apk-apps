<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Storage;

use App\Contexts\ReleaseDistribution\Application\ArtifactStorage;
use Illuminate\Support\Facades\Storage;

final class LocalArtifactStorage implements ArtifactStorage
{
    public function storeFromLocalFile(string $sourcePath, string $storagePath): void
    {
        $stream = fopen($sourcePath, 'rb');

        if ($stream === false) {
            throw new \RuntimeException('Cannot open uploaded APK.');
        }

        try {
            Storage::disk((string) config('apk.storage_disk'))->put($storagePath, $stream);
        } finally {
            fclose($stream);
        }
    }

    public function exists(string $storagePath): bool
    {
        return Storage::disk((string) config('apk.storage_disk'))->exists($storagePath);
    }

    public function size(string $storagePath): int
    {
        return Storage::disk((string) config('apk.storage_disk'))->size($storagePath);
    }

    public function absolutePath(string $storagePath): string
    {
        return Storage::disk((string) config('apk.storage_disk'))->path($storagePath);
    }

    public function delete(string $storagePath): void
    {
        Storage::disk((string) config('apk.storage_disk'))->delete($storagePath);
    }
}
