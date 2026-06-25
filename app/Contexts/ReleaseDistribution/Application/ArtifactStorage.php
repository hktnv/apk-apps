<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

interface ArtifactStorage
{
    public function storeFromLocalFile(string $sourcePath, string $storagePath): void;

    public function exists(string $storagePath): bool;

    public function size(string $storagePath): int;

    public function absolutePath(string $storagePath): string;

    public function delete(string $storagePath): void;
}
