<?php

namespace LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use LCFramework\Framework\Admin\Filament\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
