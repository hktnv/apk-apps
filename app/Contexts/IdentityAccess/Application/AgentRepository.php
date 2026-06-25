<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

use Illuminate\Support\Collection;

interface AgentRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): AgentView;

    public function find(string $id): ?AgentView;

    public function findForAuthentication(string $agentId): ?AgentAuthenticationView;

    public function existsByAgentId(string $agentId): bool;

    public function setActive(string $id, bool $active): bool;

    public function updateSecretHash(string $id, string $secretHash): bool;

    /** @return Collection<int, AgentView> */
    public function all(): Collection;
}
