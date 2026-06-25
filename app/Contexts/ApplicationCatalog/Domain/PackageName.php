<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Domain;

use InvalidArgumentException;

final readonly class PackageName
{
    public function __construct(public string $value)
    {
        if (! self::isValid($value)) {
            throw new InvalidArgumentException('Invalid Android package name.');
        }
    }

    public static function isValid(string $value): bool
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9_]*(\.[A-Za-z][A-Za-z0-9_]*)+$/', $value) === 1;
    }
}
