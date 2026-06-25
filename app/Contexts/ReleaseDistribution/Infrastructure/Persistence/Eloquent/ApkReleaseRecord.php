<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent;

use App\Contexts\ApplicationCatalog\Infrastructure\Persistence\Eloquent\ManagedApplicationRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ApkReleaseRecord extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'apk_releases';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'application_id',
        'version_code',
        'version_name',
        'release_notes',
        'original_filename',
        'storage_disk',
        'storage_path',
        'sha256',
        'size_bytes',
        'uploaded_by_admin_id',
        'created_at',
    ];

    protected $casts = [
        'version_code' => 'integer',
        'size_bytes' => 'integer',
        'created_at' => 'datetime',
    ];

    /** @return BelongsTo<ManagedApplicationRecord, $this> */
    public function application(): BelongsTo
    {
        return $this->belongsTo(ManagedApplicationRecord::class, 'application_id');
    }
}
