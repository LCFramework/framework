<?php

namespace LCFramework\Framework\Auth\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LCFramework\Framework\Auth\Contracts\ShouldVerifyEmail;
use LCFramework\Framework\Auth\Notifications\EmailVerification;
use LCFramework\Framework\Transformer\Facade\Transformer;

class User extends Authenticatable implements ShouldVerifyEmail, HasName
{
    use Notifiable;

    const CREATED_AT = 'create_date';

    const UPDATED_AT = 'update_time';

    protected $primaryKey = 'user_code';

    public function shouldVerifyEmail(): bool
    {
        return config('lcframework.auth.require_email_verification');
    }

    public function getFilamentName(): string
    {
        return $this->user_id;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new EmailVerification());
    }

    public function getTable(): string
    {
        return config('lcframework.last_chaos.database.auth').'.bg_user';
    }

    public function getFillable(): array
    {
        return Transformer::transform(
            'auth.user.fillable',
            [
                'user_id',
                'email',
                'passwd',
                'email_verified_at',
            ]
        );
    }

    public function getHidden(): array
    {
        return Transformer::transform(
            'auth.user.fillable',
            [
                'passwd',
                'remember_token',
            ]
        );
    }

    public function getCasts(): array
    {
        return Transformer::transform(
            'auth.user.fillable',
            [
                'email_verified_at' => 'datetime',
            ]
        );
    }

    public function getAuthPassword(): string
    {
        return $this->passwd;
    }
}
