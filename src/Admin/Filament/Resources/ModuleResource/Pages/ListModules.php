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

        $record->forceFill(['status' => 'enabled'])->save();

        Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been successfully enabled', $record->name))
            ->body('This includes any dependency modules')
            ->send();
    }

    public function disableModule(Module $record): void
    {
        Modules::disable($record->name);

        $record->forceFill(['status' => 'disabled'])->save();

        Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been successfully disabled', $record->name))
            ->body('This includes any dependent modules')
            ->send();
    }

    public function deleteModule(Module $record): void
    {
        if (Modules::delete($record->name)) {
            $record->delete();

            Notification::make()
                ->success()
                ->title(sprintf('Module "%s" has been successfully deleted', $record->name))
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title(sprintf('Module "%s" has been unsuccessfully deleted', $record->name))
                ->body('LCFramework may not have writable permissions to the module directory')
                ->send();
        }
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('enable')
                ->label('Enable')
                ->button()
                ->hidden(fn (Module $record): bool => $record->enabled)
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->action('enableModule'),
            Action::make('disable')
                ->label('Disable')
                ->button()
                ->hidden(fn (Module $record): bool => $record->disabled)
                ->icon('heroicon-o-x')
                ->requiresConfirmation()
                ->action('disableModule'),
            Action::make('delete')
                ->label('Delete')
                ->button()
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action('deleteModule'),
        ];
    }
}
