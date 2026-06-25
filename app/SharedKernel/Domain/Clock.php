<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
