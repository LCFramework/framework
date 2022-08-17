<?php

namespace LCFramework\Framework\Installer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;
use LCFramework\Framework\LCFramework;

class EnsureFrameworkIsInstalled
{
    public function handle($request, Closure $next)
    {
        if (LCFramework::installed()) {
            return $next($request);
        }

        $url = URL::route('installer');
        if (
            $request->url() === $url ||
            str_starts_with($request->path(), 'livewire')
        ) {
            return $next($request);
        }

        return $request->expectsJson()
            ? abort(403, 'LCFramework is not installed.')
            : redirect($url);
    }
}
