<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

use Illuminate\Support\Collection;

final class ListApplications
{
    public function __construct(private readonly ManagedApplicationRepository $applications) {}

    /** @return Collection<int, ManagedApplicationView> */
    public function execute(): Collection
    {
        return $this->applications->all();
    }
}
