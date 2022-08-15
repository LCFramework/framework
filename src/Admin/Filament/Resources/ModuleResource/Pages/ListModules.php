<?php

namespace LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource;
use LCFramework\Framework\Module\Exception\InvalidModuleEnabled;
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

    public function enableBulk(Collection $modules): void
    {
        $count = 0;
        foreach ($modules as $module) {
            if ($module->enabled()) {
                continue;
            }

            try {
                Modules::enable($module);
                $module->forceFill(['status' => 'enabled'])->save();

                $count++;
            } catch (InvalidModuleEnabled) {
                Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to enable module "%s"',
                            $module->name
                        )
                    )
                    ->send();
            }
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s modules have been successfully enabled',
                    number_format($count)
                )
            )
            ->body('This includes any dependency modules')
            ->send();
    }

    public function disableBulk(Collection $modules): void
    {
        $count = 0;
        foreach ($modules as $module) {
            if ($module->disabled()) {
                continue;
            }

            Modules::disable($module);
            $module->forceFill(['status' => 'disabled'])->save();

            $count++;
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s modules have been successfully disabled',
                    number_format($count)
                )
            )
            ->body('This includes any dependency modules')
            ->body('This includes any dependent modules')
            ->send();
    }

    public function deleteBulk(Collection $modules): void
    {
        $count = 0;
        foreach ($modules as $module) {
            if (!Modules::delete($module)) {
                Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to delete module "%s"',
                            $module->name
                        )
                    )
                    ->body('LCFramework may not have writable permissions to the module directory')
                    ->send();

                continue;
            }

            $module->delete();

            $count++;
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s modules have been successfully deleted',
                    number_format($count)
                )
            )
            ->send();
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('enable')
                ->label('Enable')
                ->button()
                ->hidden(fn(Module $record): bool => $record->enabled)
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->action('enableModule'),
            Action::make('disable')
                ->label('Disable')
                ->button()
                ->hidden(fn(Module $record): bool => $record->disabled)
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
