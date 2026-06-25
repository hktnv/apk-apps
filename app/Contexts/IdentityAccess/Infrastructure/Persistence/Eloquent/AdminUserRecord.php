<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Infrastructure\Persistence\Eloquent;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class AdminUserRecord extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_users';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
