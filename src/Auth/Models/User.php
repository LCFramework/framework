<?php

namespace LCFramework\Framework\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LCFramework\Framework\Auth\Contracts\OptionallyVerifyEmail;

class User extends Authenticatable implements OptionallyVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function shouldVerifyEmail(): bool
    {
        return config('lcframework.auth.require_email_verification');
    }
}
