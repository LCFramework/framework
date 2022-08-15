<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LCFramework\Framework\Auth\Contracts\ShouldVerifyEmail;

class EmailVerificationController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        if (
            $user->hasVerifiedEmail() ||
            ($user instanceof ShouldVerifyEmail && !$user->shouldVerifyEmail())
        ) {
            return redirect()->intended();
        }

        return view('lcframework::auth.email-verification');
    }

    public function store(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended();
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        Notification::make()
            ->success()
            ->title('Email has been successfully verified')
            ->send();

        return redirect()->intended();
    }
}
