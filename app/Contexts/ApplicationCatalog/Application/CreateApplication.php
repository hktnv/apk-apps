<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

use App\Contexts\ApplicationCatalog\Domain\PackageName;
use App\SharedKernel\Domain\IdentifierGenerator;
use App\SharedKernel\Domain\OperationResult;
use InvalidArgumentException;

final class CreateApplication
{
    public function __construct(
        private readonly ManagedApplicationRepository $applications,
        private readonly IdentifierGenerator $ids,
    ) {}

    public function execute(CreateApplicationCommand $command): OperationResult
    {
        try {
            new PackageName($command->packageName);
        } catch (InvalidArgumentException) {
            return OperationResult::failure('INVALID_PACKAGE_NAME', 'Android package adı geçerli değil.');
        }

        if ($this->applications->existsBySlug($command->slug)) {
            return OperationResult::failure('DUPLICATE_SLUG', 'Bu slug zaten kullanılıyor.');
        }

        if ($this->applications->existsByPackageName($command->packageName)) {
            return OperationResult::failure('DUPLICATE_PACKAGE_NAME', 'Bu package name zaten kullanılıyor.');
        }

        return OperationResult::success($this->applications->create([
            'id' => $this->ids->newUlid(),
            'name' => $command->name,
            'slug' => $command->slug,
            'package_name' => $command->packageName,
            'description' => $command->description,
            'is_active' => true,
        ]));
    }
}
