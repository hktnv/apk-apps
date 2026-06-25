<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

final readonly class OperationResult
{
    private function __construct(
        public bool $ok,
        public mixed $value,
        public ?string $errorCode,
        public ?string $message,
    ) {}

    public static function success(mixed $value = null): self
    {
        return new self(true, $value, null, null);
    }

    public static function failure(string $errorCode, string $message): self
    {
        return new self(false, null, $errorCode, $message);
    }
}
