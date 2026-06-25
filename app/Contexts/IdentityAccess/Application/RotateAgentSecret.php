<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

use App\SharedKernel\Domain\OperationResult;

final class RotateAgentSecret
{
    public function __construct(
        private readonly AgentRepository $agents,
        private readonly AgentSecretHasher $hasher,
        private readonly AgentSecretGenerator $secrets,
    ) {}

    public function execute(string $id): OperationResult
    {
        $agent = $this->agents->find($id);
        if ($agent === null) {
            return OperationResult::failure('AGENT_NOT_FOUND', 'Agent bulunamadı.');
        }

        $secret = $this->secrets->newSecret();
        $this->agents->updateSecretHash($id, $this->hasher->hash($secret));

        return OperationResult::success(new RotatedAgentSecretView($agent, $secret));
    }
}
