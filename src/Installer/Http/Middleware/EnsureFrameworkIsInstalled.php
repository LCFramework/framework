<?php

namespace LCFramework\Framework\Installer\Http\Middleware;

use Closure;
use LCFramework\Framework\LCFramework;

class EnsureFrameworkIsInstalled
{
    public function handle($request, Closure $next)
    {
        if (LCFramework::installed()) {
            return $next($request);
        }

        if ($request->routeIs('installer/*')) {
            return $next($request);
        }

        return $request->expectsJson()
            ? abort(403, 'LCFramework is not installed.')
            : redirect()->route('installer');
    }
}
