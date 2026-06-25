<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

final readonly class UpdateApplicationDetailsCommand
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $description,
    ) {}
}
