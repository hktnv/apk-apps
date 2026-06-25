<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Domain;

use InvalidArgumentException;

final readonly class VersionCode
{
    public function __construct(public int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Version code must be positive.');
        }
    }
}
