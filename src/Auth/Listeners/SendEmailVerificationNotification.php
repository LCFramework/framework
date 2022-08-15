<?php

namespace LCFramework\Framework\Auth\Listeners;

use Illuminate\Auth\Events\Registered;
use LCFramework\Framework\Auth\Contracts\ShouldVerifyEmail;

class SendEmailVerificationNotification
{
    public function handle(Registered $event): void
    {
        if (
            $event->user instanceof ShouldVerifyEmail &&
            $event->user->shouldVerifyEmail() &&
            ! $event->user->hasVerifiedEmail()
        ) {
            $event->user->sendEmailVerificationNotification();
        }
    }
}
