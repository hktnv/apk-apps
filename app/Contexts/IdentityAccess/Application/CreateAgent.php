<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

use App\SharedKernel\Domain\IdentifierGenerator;
use App\SharedKernel\Domain\OperationResult;

final class CreateAgent
{
    public function __construct(
        private readonly AgentRepository $agents,
        private readonly AgentSecretHasher $hasher,
        private readonly AgentSecretGenerator $secrets,
        private readonly IdentifierGenerator $ids,
    ) {}

    public function execute(CreateAgentCommand $command): OperationResult
    {
        $agentId = $this->newAgentId();
        $secret = $this->secrets->newSecret();

        $agent = $this->agents->create([
            'id' => $this->ids->newUlid(),
            'name' => $command->name,
            'agent_id' => $agentId,
            'secret_hash' => $this->hasher->hash($secret),
            'is_active' => true,
            'created_by_admin_id' => $command->createdByAdminId,
        ]);

        return OperationResult::success(new CreatedAgentView($agent, $secret));
    }

    private function newAgentId(): string
    {
        do {
            $agentId = 'agt_'.bin2hex(random_bytes(12));
        } while ($this->agents->existsByAgentId($agentId));

        return $agentId;
    }
}
