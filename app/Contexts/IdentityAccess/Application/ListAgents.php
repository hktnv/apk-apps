<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

use Illuminate\Support\Collection;

final class ListAgents
{
    public function __construct(private readonly AgentRepository $agents) {}

    /** @return Collection<int, AgentView> */
    public function execute(): Collection
    {
        return $this->agents->all();
    }
}
