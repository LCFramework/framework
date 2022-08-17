<?php

namespace LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\ListRecords;
use LCFramework\Framework\Admin\Filament\Resources\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
}
