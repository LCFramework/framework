<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function destroy(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Notification::make()
            ->success()
            ->title('You have been successfully logged out')
            ->send();

        return redirect()->route('login');
    }
}
