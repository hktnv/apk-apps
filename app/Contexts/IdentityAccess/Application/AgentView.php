<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

final readonly class AgentView
{
    public function __construct(
        public string $id,
        public string $name,
        public string $agentId,
        public bool $isActive,
        public string $createdByAdminId,
        public string $createdAt,
    ) {}
}
