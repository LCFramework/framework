<?php

namespace LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource;
use LCFramework\Framework\Module\Facade\Modules;
use LCFramework\Framework\Module\Models\Module;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    public function enableModule(Module $record): void
    {
        Modules::enable($record->name);

        Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been enabled', $record->name))
            ->body('This includes any dependency modules')
            ->send();
    }

    public function disableModule(Module $record): void
    {
        Modules::disable($record->name);

        Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been disabled', $record->name))
            ->body('This includes any dependent modules')
            ->send();
    }

    public function deleteModule(Module $record): void
    {
        if ($record->enabled) {
            Modules::disable($record);
        }

        Modules::clearCache();
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('enable')
                ->label('Enable')
                ->button()
                ->hidden(fn (Module $record): bool => $record->enabled)
                ->requiresConfirmation()
                ->action('enableModule'),
            Action::make('disable')
                ->label('Disable')
                ->button()
                ->hidden(fn (Module $record): bool => $record->disabled)
                ->requiresConfirmation()
                ->action('disableModule'),
            Action::make('delete')
                ->label('Delete')
                ->button()
                ->color('danger')
                ->requiresConfirmation()
                ->action('deleteModule'),
        ];
    }
}
