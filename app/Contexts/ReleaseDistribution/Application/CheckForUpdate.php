<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use App\Contexts\ApplicationCatalog\Application\ManagedApplicationRepository;
use App\Contexts\ReleaseDistribution\Domain\Channel;
use App\Contexts\ReleaseDistribution\Domain\UpdateDecisionPolicy;
use App\SharedKernel\Domain\OperationResult;
use ValueError;

final class CheckForUpdate
{
    public function __construct(
        private readonly ManagedApplicationRepository $applications,
        private readonly PublicationRepository $publications,
        private readonly ReleaseRepository $releases,
        private readonly UpdateDecisionPolicy $policy,
    ) {}

    public function execute(string $packageName, string $channelValue, int $currentVersionCode): OperationResult
    {
        try {
            $channel = Channel::from($channelValue);
        } catch (ValueError) {
            return OperationResult::failure('INVALID_CHANNEL', 'Kanal geçerli değil.');
        }

        $application = $this->applications->findActiveByPackageName($packageName);
        if ($application === null) {
            return OperationResult::failure('APPLICATION_NOT_FOUND', 'Uygulama bulunamadı.');
        }

        $publication = $this->publications->current($application->id, $channel->value);
        $release = $publication === null ? null : $this->releases->find($publication->releaseId);
        $decision = $this->policy->decide(
            $currentVersionCode,
            $release?->versionCode,
            $publication === null ? false : $publication->forceUpdate,
            $publication === null ? 0 : $publication->minimumSupportedVersionCode,
        );

        return OperationResult::success(new CheckForUpdateResult(
            $application->id,
            $packageName,
            $channel->value,
            $currentVersionCode,
            $decision,
            $release,
        ));
    }
}
