<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class AgentRecord extends Model
{
    protected $table = 'agents';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'agent_id',
        'secret_hash',
        'is_active',
        'created_by_admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'secret_hash',
    ];
}
