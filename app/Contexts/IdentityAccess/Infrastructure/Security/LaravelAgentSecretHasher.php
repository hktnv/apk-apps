<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Infrastructure\Security;

use App\Contexts\IdentityAccess\Application\AgentSecretHasher;
use Illuminate\Support\Facades\Hash;

final class LaravelAgentSecretHasher implements AgentSecretHasher
{
    public function hash(string $secret): string
    {
        return Hash::make($secret);
    }

    public function check(string $secret, string $hash): bool
    {
        return Hash::check($secret, $hash);
    }
}
