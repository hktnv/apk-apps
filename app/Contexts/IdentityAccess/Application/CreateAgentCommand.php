<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

final readonly class CreateAgentCommand
{
    public function __construct(
        public string $name,
        public string $createdByAdminId,
    ) {}
}
