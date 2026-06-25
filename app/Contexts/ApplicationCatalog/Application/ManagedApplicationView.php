<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

final readonly class ManagedApplicationView
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $packageName,
        public ?string $description,
        public bool $isActive,
    ) {}
}
