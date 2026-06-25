<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

use App\SharedKernel\Domain\OperationResult;

final class UpdateApplicationDetails
{
    public function __construct(private readonly ManagedApplicationRepository $applications) {}

    public function execute(UpdateApplicationDetailsCommand $command): OperationResult
    {
        if ($this->applications->find($command->id) === null) {
            return OperationResult::failure('APPLICATION_NOT_FOUND', 'Uygulama bulunamadı.');
        }

        if ($this->applications->existsBySlug($command->slug, $command->id)) {
            return OperationResult::failure('DUPLICATE_SLUG', 'Bu slug zaten kullanılıyor.');
        }

        $this->applications->update($command->id, [
            'name' => $command->name,
            'slug' => $command->slug,
            'description' => $command->description,
        ]);

        return OperationResult::success($this->applications->find($command->id));
    }
}
