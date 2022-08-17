<?php

namespace LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use LCFramework\Framework\Admin\Filament\Resources\UserResource;
use LCFramework\Framework\Auth\Models\User;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            ...parent::getActions(),
            Action::make('ban')
                ->label('Ban')
                ->requiresConfirmation()
                ->hidden(fn(User $record) => $record->is_banned)
                ->action(fn(User $record) => $record->ban())
        ];
    }
}
