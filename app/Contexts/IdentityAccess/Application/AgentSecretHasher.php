<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

interface AgentSecretHasher
{
    public function hash(string $secret): string;

    public function check(string $secret, string $hash): bool;
}
