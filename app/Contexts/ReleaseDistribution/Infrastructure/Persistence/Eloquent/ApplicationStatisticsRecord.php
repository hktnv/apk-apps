<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Infrastructure\Persistence\Eloquent;

use App\Contexts\ApplicationCatalog\Infrastructure\Persistence\Eloquent\ManagedApplicationRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ApplicationStatisticsRecord extends Model
{
    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected $table = 'application_statistics';

    protected $primaryKey = 'application_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'application_id',
        'update_check_count',
        'update_available_count',
        'up_to_date_count',
        'apk_download_count',
        'last_checked_at',
        'last_downloaded_at',
    ];

    protected $casts = [
        'update_check_count' => 'integer',
        'update_available_count' => 'integer',
        'up_to_date_count' => 'integer',
        'apk_download_count' => 'integer',
        'last_checked_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    /** @return BelongsTo<ManagedApplicationRecord, $this> */
    public function application(): BelongsTo
    {
        return $this->belongsTo(ManagedApplicationRecord::class, 'application_id');
    }
}
