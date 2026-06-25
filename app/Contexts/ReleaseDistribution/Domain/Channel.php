<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Domain;

enum Channel: string
{
    case Stable = 'stable';
    case Beta = 'beta';
    case Internal = 'internal';
}
