<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use App\SharedKernel\Domain\OperationResult;

interface ApkFileValidator
{
    public function validate(string $localPath, string $originalFilename, int $maxBytes): OperationResult;
}
