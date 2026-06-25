<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Domain;

enum UpdateStatus: string
{
    case NoPublishedRelease = 'NO_PUBLISHED_RELEASE';
    case UpdateAvailable = 'UPDATE_AVAILABLE';
    case UpToDate = 'UP_TO_DATE';
    case ClientAhead = 'CLIENT_AHEAD';
}
