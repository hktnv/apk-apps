<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

final class GetApplicationDetails
{
    public function __construct(private readonly ManagedApplicationRepository $applications) {}

    public function execute(string $id): ?ManagedApplicationView
    {
        return $this->applications->find($id);
    }
}
