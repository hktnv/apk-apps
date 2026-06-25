<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

use App\SharedKernel\Domain\OperationResult;

final class SetApplicationActiveStatus
{
    public function __construct(private readonly ManagedApplicationRepository $applications) {}

    public function execute(string $id, bool $isActive): OperationResult
    {
        if ($this->applications->find($id) === null) {
            return OperationResult::failure('APPLICATION_NOT_FOUND', 'Uygulama bulunamadı.');
        }

        $this->applications->update($id, ['is_active' => $isActive]);

        return OperationResult::success($this->applications->find($id));
    }
}
