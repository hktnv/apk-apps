<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure;

use App\SharedKernel\Domain\IdentifierGenerator;
use Illuminate\Support\Str;

final class UlidIdentifierGenerator implements IdentifierGenerator
{
    public function newUlid(): string
    {
        return (string) Str::ulid();
    }
}
