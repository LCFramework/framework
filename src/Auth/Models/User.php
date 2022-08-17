<?php

namespace LCFramework\Framework\Auth\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LCFramework\Framework\Auth\Contracts\ShouldVerifyEmail;
use LCFramework\Framework\Auth\Notifications\EmailVerification;
use LCFramework\Framework\LastChaos\Models\Character;
use LCFramework\Framework\LastChaos\Models\UserMeta;
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
            'auth.user.hidden',
            [
                'passwd',
                'remember_token',
            ]
        );
    }

    public function getCasts(): array
    {
        return Transformer::transform(
            'auth.user.casts',
            [
                ...parent::getCasts(),
                'email_verified_at' => 'datetime',
            ]
        );
    }

    public function getAuthPassword(): string
    {
        return $this->passwd;
    }

    public function characters(): HasMany
    {
        return $this->hasMany(
            Character::class,
            'a_user_index',
            'user_code'
        );
    }

    public function meta(): HasOne
    {
        return $this->hasOne(
            UserMeta::class,
            'a_idname',
            'user_id'
        );
    }

    public function ban(): void
    {
        $this->meta?->forceFill([
            'a_enable' => false,
        ])->save();
    }

    public function unban(): void
    {
        $this->meta?->forceFill([
            'a_enable' => true,
        ])->save();
    }

    public function isBanned(): Attribute
    {
        return Attribute::make(
            get: function () {
                $meta = $this->meta;
                if ($meta === null) {
                    return false;
                }

                return ! $meta->a_enable;
            }
        );
    }

    public function isOnline(): Attribute
    {
        return Attribute::make(
            get: function () {
                $meta = $this->meta;
                if ($meta === null) {
                    return false;
                }

                return $meta->a_zone_num !== -1;
            }
        );
    }
}
