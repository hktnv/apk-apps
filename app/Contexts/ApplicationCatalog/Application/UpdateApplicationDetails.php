<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

use App\SharedKernel\Domain\OperationResult;

final class UpdateApplicationDetails
{
    public function __construct(private readonly ManagedApplicationRepository $applications) {}

    public function execute(UpdateApplicationDetailsCommand $command): OperationResult
    {
        $application = $this->applications->find($command->id);
        if ($application === null) {
            return OperationResult::failure('APPLICATION_NOT_FOUND', 'Uygulama bulunamadı.');
        }

        $this->applications->update($command->id, [
            'name' => $command->name,
            'slug' => $application->slug,
            'description' => $command->description,
        ]);

        return OperationResult::success($this->applications->find($command->id));
    }
}
