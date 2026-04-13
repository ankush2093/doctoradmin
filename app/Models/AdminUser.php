<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'admin_users';

    protected $fillable = [
        'userName',
        'password',
        'adminRole',
        'isActive',
    ];

    protected $hidden = [
        'password',
    ];

      public function role()
    {
        return $this->belongsTo(Role::class, 'adminRole');
    }
}
