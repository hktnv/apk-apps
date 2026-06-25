<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Domain;

final readonly class UpdateDecision
{
    public function __construct(
        public UpdateStatus $status,
        public ?int $publishedVersionCode,
        public ?bool $required,
    ) {}
}
