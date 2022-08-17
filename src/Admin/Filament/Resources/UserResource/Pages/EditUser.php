<?php

namespace LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use LCFramework\Framework\Admin\Filament\Resources\UserResource;

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
                ->hidden(fn () => $this->record->is_banned || $this->record->user_code === auth()->id())
                ->action(function () {
                    $this->record->ban();

                    Notification::make()
                        ->success()
                        ->title('User has been successfully banned')
                        ->send();
                }),
            Action::make('unban')
                ->label('Unban')
                ->requiresConfirmation()
                ->hidden(fn () => ! $this->record->is_banned || $this->record->user_code === auth()->id())
                ->action(function () {
                    $this->record->unban();

                    Notification::make()
                        ->success()
                        ->title('User has been successfully unbanned')
                        ->send();
                }),
        ];
    }
}
