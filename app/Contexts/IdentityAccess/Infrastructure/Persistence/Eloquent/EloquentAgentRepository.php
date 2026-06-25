<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Infrastructure\Persistence\Eloquent;

use App\Contexts\IdentityAccess\Application\AgentAuthenticationView;
use App\Contexts\IdentityAccess\Application\AgentRepository;
use App\Contexts\IdentityAccess\Application\AgentView;
use Illuminate\Support\Collection;

final class EloquentAgentRepository implements AgentRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): AgentView
    {
        return $this->map(AgentRecord::query()->create($attributes));
    }

    public function find(string $id): ?AgentView
    {
        $record = AgentRecord::query()->find($id);

        return $record instanceof AgentRecord ? $this->map($record) : null;
    }

    public function findForAuthentication(string $agentId): ?AgentAuthenticationView
    {
        $record = AgentRecord::query()->where('agent_id', $agentId)->first();

        return $record instanceof AgentRecord ? $this->mapForAuthentication($record) : null;
    }

    public function existsByAgentId(string $agentId): bool
    {
        return AgentRecord::query()->where('agent_id', $agentId)->exists();
    }

    public function setActive(string $id, bool $active): bool
    {
        return AgentRecord::query()->whereKey($id)->update(['is_active' => $active]) === 1;
    }

    /** @return Collection<int, AgentView> */
    public function all(): Collection
    {
        return AgentRecord::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (AgentRecord $record): AgentView => $this->map($record));
    }

    private function map(AgentRecord $record): AgentView
    {
        return new AgentView(
            (string) $record->getAttribute('id'),
            (string) $record->getAttribute('name'),
            (string) $record->getAttribute('agent_id'),
            (bool) $record->getAttribute('is_active'),
            (string) $record->getAttribute('created_by_admin_id'),
            (string) $record->getAttribute('created_at'),
        );
    }

    private function mapForAuthentication(AgentRecord $record): AgentAuthenticationView
    {
        return new AgentAuthenticationView(
            (string) $record->getAttribute('id'),
            (string) $record->getAttribute('name'),
            (string) $record->getAttribute('agent_id'),
            (string) $record->getAttribute('secret_hash'),
            (bool) $record->getAttribute('is_active'),
            (string) $record->getAttribute('created_by_admin_id'),
            (string) $record->getAttribute('created_at'),
        );
    }
}
