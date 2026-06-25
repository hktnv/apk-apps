<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use App\Contexts\ApplicationCatalog\Application\ListApplications;
use App\Contexts\ReleaseDistribution\Application\PublicationRepository;
use App\Contexts\ReleaseDistribution\Application\ReleaseRepository;
use Illuminate\View\View;

final class DashboardController
{
    public function __construct(
        private readonly ListApplications $applications,
        private readonly ReleaseRepository $releases,
        private readonly PublicationRepository $publications,
    ) {}

    public function __invoke(): View
    {
        $applications = $this->applications->execute();

        return view('admin.dashboard', [
            'activeApplicationCount' => $applications->where('isActive', true)->count(),
            'releaseCount' => $this->releases->count(),
            'currentPublications' => $this->publications->currentByChannel(),
            'latestReleases' => $this->releases->latest(5),
        ]);
    }
}
