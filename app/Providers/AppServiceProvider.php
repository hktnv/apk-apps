<?php

namespace App\Providers;

use App\Contexts\ApplicationCatalog\Application\ManagedApplicationRepository;
use App\Contexts\ApplicationCatalog\Infrastructure\Persistence\Eloquent\EloquentManagedApplicationRepository;
use App\Contexts\IdentityAccess\Application\AgentRepository;
use App\Contexts\IdentityAccess\Application\AgentSecretHasher;
use App\Contexts\IdentityAccess\Infrastructure\Console\CreateAdminUserCommand;
use App\Contexts\IdentityAccess\Infrastructure\Persistence\Eloquent\EloquentAgentRepository;
use App\Contexts\IdentityAccess\Infrastructure\Security\LaravelAgentSecretHasher;
use App\Contexts\ReleaseDistribution\Application\ApkFileValidator;
use App\Contexts\ReleaseDistribution\Application\ArtifactStorage;
use App\Contexts\ReleaseDistribution\Application\PublicationRepository;
use App\Contexts\ReleaseDistribution\Application\ReleaseRepository;
use App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent\EloquentPublicationRepository;
use App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent\EloquentReleaseRepository;
use App\Contexts\ReleaseDistribution\Infrastructure\Storage\LocalArtifactStorage;
use App\Contexts\ReleaseDistribution\Infrastructure\Validation\ZipApkFileValidator;
use App\SharedKernel\Domain\Clock;
use App\SharedKernel\Domain\IdentifierGenerator;
use App\SharedKernel\Domain\TransactionRunner;
use App\SharedKernel\Infrastructure\LaravelTransactionRunner;
use App\SharedKernel\Infrastructure\SystemClock;
use App\SharedKernel\Infrastructure\UlidIdentifierGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IdentifierGenerator::class, UlidIdentifierGenerator::class);
        $this->app->bind(Clock::class, SystemClock::class);
        $this->app->bind(TransactionRunner::class, LaravelTransactionRunner::class);
        $this->app->bind(ManagedApplicationRepository::class, EloquentManagedApplicationRepository::class);
        $this->app->bind(AgentRepository::class, EloquentAgentRepository::class);
        $this->app->bind(AgentSecretHasher::class, LaravelAgentSecretHasher::class);
        $this->app->bind(ReleaseRepository::class, EloquentReleaseRepository::class);
        $this->app->bind(PublicationRepository::class, EloquentPublicationRepository::class);
        $this->app->bind(ArtifactStorage::class, LocalArtifactStorage::class);
        $this->app->bind(ApkFileValidator::class, ZipApkFileValidator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateAdminUserCommand::class,
            ]);
        }
    }
}
