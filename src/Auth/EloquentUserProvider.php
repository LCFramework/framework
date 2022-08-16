<?php

namespace LCFramework\Framework\Auth;

use Illuminate\Auth\EloquentUserProvider as UserProviderBase;
use Illuminate\Contracts\Auth\Authenticatable;

class EloquentUserProvider extends UserProviderBase
{
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = $credentials['password'];

        return $this->hasher->check(
            $plain,
            $user->getAuthPassword(),
            [
                'username' => $user->user_id,
            ]
        );
    }
}
