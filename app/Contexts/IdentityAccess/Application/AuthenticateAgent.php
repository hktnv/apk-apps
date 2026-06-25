<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

use App\SharedKernel\Domain\OperationResult;

final class AuthenticateAgent
{
    public function __construct(
        private readonly AgentRepository $agents,
        private readonly AgentSecretHasher $hasher,
    ) {}

    public function execute(string $agentId, string $secret): OperationResult
    {
        $agent = $this->agents->findForAuthentication($agentId);
        if ($agent === null || ! $this->hasher->check($secret, $agent->secretHash)) {
            return OperationResult::failure('AGENT_AUTH_FAILED', 'Agent bilgileri geçerli değil.');
        }

        if (! $agent->isActive) {
            return OperationResult::failure('AGENT_INACTIVE', 'Agent pasif durumda.');
        }

        return OperationResult::success($agent->toAgentView());
    }
}
