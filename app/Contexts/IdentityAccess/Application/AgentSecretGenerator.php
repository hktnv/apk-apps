<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

final class AgentSecretGenerator
{
    public function newSecret(): string
    {
        return 'sec_'.bin2hex(random_bytes(32));
    }
}
