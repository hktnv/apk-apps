<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure;

use App\SharedKernel\Domain\TransactionRunner;
use Illuminate\Support\Facades\DB;
use Throwable;

final class LaravelTransactionRunner implements TransactionRunner
{
    /**
     * @template TResult
     *
     * @param  callable(): TResult  $operation
     * @return TResult
     */
    public function run(callable $operation): mixed
    {
        DB::beginTransaction();

        try {
            $result = $operation();

            DB::commit();

            return $result;
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
