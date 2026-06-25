<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Application;

use App\SharedKernel\Domain\OperationResult;

final class SetAgentActiveStatus
{
    public function __construct(private readonly AgentRepository $agents) {}

    public function execute(string $id, bool $active): OperationResult
    {
        if ($this->agents->find($id) === null) {
            return OperationResult::failure('AGENT_NOT_FOUND', 'Agent bulunamadı.');
        }

        $this->agents->setActive($id, $active);

        return OperationResult::success();
    }
}
