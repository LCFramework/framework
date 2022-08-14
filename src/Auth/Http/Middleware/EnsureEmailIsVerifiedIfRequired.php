<?php

namespace LCFramework\Framework\Auth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use LCFramework\Framework\Auth\Contracts\OptionallyVerifyEmail;

class EnsureEmailIsVerifiedIfRequired
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $user = $request->user();

        if (
            ! $user ||
            (
                $user instanceof OptionallyVerifyEmail &&
                $user->shouldVerifyEmail() &&
                ! $user->hasVerifiedEmail()
            )
        ) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}
