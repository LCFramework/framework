<?php

namespace LCFramework\Framework\Admin\Http\Middleware;

use Filament\Http\Middleware\Authenticate as AuthenticateBase;

class Authenticate extends AuthenticateBase
{
    protected function redirectTo($request): string
    {
        return route('login');
    }
}
