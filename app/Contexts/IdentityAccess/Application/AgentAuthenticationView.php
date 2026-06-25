<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

final readonly class AgentAuthenticationView
{
    public function __construct(
        public string $id,
        public string $name,
        public string $agentId,
        public string $secretHash,
        public bool $isActive,
        public string $createdByAdminId,
        public string $createdAt,
    ) {}

    public function toAgentView(): AgentView
    {
        return new AgentView(
            $this->id,
            $this->name,
            $this->agentId,
            $this->isActive,
            $this->createdByAdminId,
            $this->createdAt,
        );
    }
}
