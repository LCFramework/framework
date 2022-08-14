<?php

namespace LCFramework\Framework\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use LCFramework\Framework\Auth\Contracts\OptionallyVerifyEmail;

class User extends Authenticatable implements OptionallyVerifyEmail
{
    public function shouldVerifyEmail(): bool
    {
        return config('lcframework.auth.require_email_verification');
    }
}
