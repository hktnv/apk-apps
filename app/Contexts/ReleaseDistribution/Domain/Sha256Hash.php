<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Domain;

use InvalidArgumentException;

final readonly class Sha256Hash
{
    public function __construct(public string $value)
    {
        if (preg_match('/^[a-f0-9]{64}$/', $value) !== 1) {
            throw new InvalidArgumentException('Invalid SHA-256 hash.');
        }
    }
}
