<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

final readonly class CreateApplicationCommand
{
    public function __construct(
        public string $name,
        public string $packageName,
        public ?string $description,
    ) {}
}
