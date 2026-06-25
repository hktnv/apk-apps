<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Infrastructure\Console;

use App\Contexts\IdentityAccess\Infrastructure\Persistence\Eloquent\AdminUserRecord;
use App\SharedKernel\Domain\IdentifierGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

final class CreateAdminUserCommand extends Command
{
    protected $signature = 'admin:create {--email=} {--name=} {--password=}';

    protected $description = 'Create an administrator account.';

    public function handle(IdentifierGenerator $ids): int
    {
        $email = (string) ($this->option('email') ?: $this->ask('E-posta'));
        $name = (string) ($this->option('name') ?: $this->ask('Ad'));
        $password = (string) ($this->option('password') ?: $this->secret('Parola'));

        if ($email === '' || $name === '' || $password === '') {
            $this->error('E-posta, ad ve parola zorunludur.');

            return self::FAILURE;
        }

        if (AdminUserRecord::query()->where('email', $email)->exists()) {
            $this->error('Bu e-posta adresiyle bir yönetici zaten var.');

            return self::FAILURE;
        }

        AdminUserRecord::query()->create([
            'id' => $ids->newUlid(),
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('Yönetici oluşturuldu.');

        return self::SUCCESS;
    }
}
