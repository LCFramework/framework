<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController
{
    public function create()
    {
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
