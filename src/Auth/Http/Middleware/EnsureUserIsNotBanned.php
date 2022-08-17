<?php

namespace LCFramework\Framework\Auth\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsNotBanned
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user === null || ! $user->is_banned) {
            return $next($request);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Notification::make()
            ->danger()
            ->title('Your account has been banned')
            ->body('You have been logged out')
            ->send();

        return redirect()->route('login');
    }
}
