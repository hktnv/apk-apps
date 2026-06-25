<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

final readonly class RotatedAgentSecretView
{
    public function __construct(
        public AgentView $agent,
        public string $secret,
    ) {}
}
