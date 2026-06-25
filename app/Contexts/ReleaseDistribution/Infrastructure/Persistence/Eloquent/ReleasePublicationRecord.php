<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class ReleasePublicationRecord extends Model
{
    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected $table = 'release_publications';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'application_id',
        'release_id',
        'channel',
        'action',
        'force_update',
        'minimum_supported_version_code',
        'comment',
        'published_by_admin_id',
        'published_at',
    ];

    protected $casts = [
        'force_update' => 'boolean',
        'minimum_supported_version_code' => 'integer',
        'published_at' => 'datetime',
    ];
}
