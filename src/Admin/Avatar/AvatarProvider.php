<?php

namespace LCFramework\Framework\Admin\Avatar;

use Filament\AvatarProviders\Contracts\AvatarProvider as AvatarProviderInterface;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AvatarProvider implements AvatarProviderInterface
{
    public function get(Model $user): string
    {
        $name = Str::of(Filament::getUserName($user))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=171717';
    }
}
