<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class ManagedApplicationRecord extends Model
{
    protected $table = 'managed_applications';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'package_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
