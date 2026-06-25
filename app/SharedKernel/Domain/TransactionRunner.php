<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

interface TransactionRunner
{
    /**
     * @template TResult
     *
     * @param  callable(): TResult  $operation
     * @return TResult
     */
    public function run(callable $operation): mixed;
}
