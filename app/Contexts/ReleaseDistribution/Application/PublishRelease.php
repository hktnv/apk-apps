<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Application;

use App\Contexts\ApplicationCatalog\Application\ManagedApplicationRepository;
use App\Contexts\ReleaseDistribution\Domain\Channel;
use App\SharedKernel\Domain\Clock;
use App\SharedKernel\Domain\IdentifierGenerator;
use App\SharedKernel\Domain\OperationResult;
use ValueError;

final class PublishRelease
{
    public function __construct(
        private readonly ManagedApplicationRepository $applications,
        private readonly ReleaseRepository $releases,
        private readonly PublicationRepository $publications,
        private readonly IdentifierGenerator $ids,
        private readonly Clock $clock,
    ) {}

    public function execute(PublishReleaseCommand $command): OperationResult
    {
        return $this->writePublication($command, 'publish', false);
    }

    public function rollback(PublishReleaseCommand $command): OperationResult
    {
        return $this->writePublication($command, 'rollback', true);
    }

    private function writePublication(PublishReleaseCommand $command, string $action, bool $allowDowngrade): OperationResult
    {
        try {
            $channel = Channel::from($command->channel);
        } catch (ValueError) {
            return OperationResult::failure('INVALID_CHANNEL', 'Kanal geçerli değil.');
        }

        $application = $this->applications->find($command->applicationId);
        $release = $this->releases->find($command->releaseId);

        if ($application === null || $release === null || $release->applicationId !== $application->id) {
            return OperationResult::failure('RELEASE_NOT_FOUND', 'Release bulunamadı.');
        }

        if ($command->minimumSupportedVersionCode > $release->versionCode) {
            return OperationResult::failure('INVALID_MINIMUM_SUPPORTED_VERSION', 'Minimum desteklenen version code yayınlanan release değerinden büyük olamaz.');
        }

        $current = $this->publications->current($command->applicationId, $channel->value);
        if (! $allowDowngrade && $current !== null && $release->versionCode < $current->releaseVersionCode) {
            return OperationResult::failure('PUBLISH_DOWNGRADE_REJECTED', 'Daha düşük version code normal publish ile yayınlanamaz.');
        }

        $publication = $this->publications->create([
            'id' => $this->ids->newUlid(),
            'application_id' => $application->id,
            'release_id' => $release->id,
            'channel' => $channel->value,
            'action' => $action,
            'force_update' => $command->forceUpdate,
            'minimum_supported_version_code' => $command->minimumSupportedVersionCode,
            'comment' => $command->comment,
            'published_by_admin_id' => $command->publishedByAdminId,
            'published_at' => $this->clock->now()->format('Y-m-d H:i:s'),
        ]);

        return OperationResult::success($publication);
    }
}
