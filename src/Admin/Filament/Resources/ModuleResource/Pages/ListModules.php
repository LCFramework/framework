<?php

namespace LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource;
use LCFramework\Framework\Module\Facade\Modules;
use LCFramework\Framework\Module\Models\Module;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getListeners(): array
    {
        return [
            ...parent::getListeners(),
            'modulesUpdated' => '$refresh',
        ];
    }

    public function enableModule(Module $record): void
    {
        if (! Modules::enable($record->name, $reason)) {
            Notification::make()
                ->danger()
                ->title(sprintf('Module "%s" has failed to be enabled', $record->name))
                ->body($reason)
                ->send();

            return;
        }

        $record->forceFill(['status' => 'enabled'])->save();

        Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been successfully enabled', $record->name))
            ->body('This includes any dependency modules')
            ->send();

        $this->emit('modulesUpdated');
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
        if (! Modules::delete($record->name, $reason)) {
            Notification::make()
                ->danger()
                ->title(sprintf('Module "%s" has been unsuccessfully deleted', $record->name))
                ->body($reason)
                ->send();

            return;
        }

        $record->delete();

        Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been successfully deleted', $record->name))
            ->send();
    }

    public function enableBulk(Collection $records): void
    {
        $count = 0;
        foreach ($records as $module) {
            if ($module->enabled) {
                return;
            }

            if (! Modules::enable($module->name, $reason)) {
                Notification::make()
                    ->danger()
                    ->title(sprintf('Module "%s" has failed to be enabled', $module->name))
                    ->body($reason)
                    ->send();

                continue;
            }

            $module->forceFill(['status' => 'enabled'])->save();

            $count++;
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s have been successfully enabled',
                    number_format($count),
                    Str::plural('module', $count)
                )
            )
            ->body('This includes any dependency modules')
            ->send();
    }

    public function disableBulk(Collection $records): void
    {
        $count = 0;
        foreach ($records as $module) {
            if ($module->disabled) {
                continue;
            }

            Modules::disable($module->name);
            $module->forceFill(['status' => 'disabled'])->save();

            $count++;
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s have been successfully disabled',
                    number_format($count),
                    Str::plural('count', $count)
                )
            )
            ->body('This includes any dependency modules')
            ->body('This includes any dependent modules')
            ->send();
    }

    public function deleteBulk(Collection $records): void
    {
        $count = 0;
        foreach ($records as $module) {
            if (! Modules::delete($module->name, $reason)) {
                Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to delete module "%s"',
                            $module->name
                        )
                    )
                    ->body($reason)
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
                    '%s %s have been successfully deleted',
                    number_format($count),
                    Str::plural('module', $count)
                )
            )
            ->send();
    }

    public function installModules(array $data): void
    {
        $count = 0;
        foreach ($data['modules'] as $path) {
            $file = Storage::disk('local')->path($path);

            if (Modules::install($file, $reason)) {
                $count++;
            } else {
                Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to install module "%s"',
                            basename($path)
                        )
                    )
                    ->body($reason)
                    ->send();
            }

            File::delete($file);
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s has been successfully installed',
                    number_format($count),
                    Str::plural('module', $count)
                )
            )
            ->send();
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('enable')
                ->label('Enable')
                ->hidden(fn (Module $record): bool => $record->enabled)
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->action('enableModule'),
            Action::make('disable')
                ->label('Disable')
                ->hidden(fn (Module $record): bool => $record->disabled)
                ->icon('heroicon-o-x')
                ->requiresConfirmation()
                ->action('disableModule'),
            Action::make('delete')
                ->label('Delete')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action('deleteModule'),
        ];
    }

    protected function getActions(): array
    {
        return [
            \Filament\Pages\Actions\Action::make('install')
                ->label('Install modules')
                ->action('installModules')
                ->form([
                    FileUpload::make('modules')
                        ->label('Modules')
                        ->disableLabel()
                        ->disk('local')
                        ->directory('modules-tmp')
                        ->preserveFilenames()
                        ->multiple()
                        ->minFiles(1)
                        ->acceptedFileTypes([
                            'application/zip',
                            'application/x-zip-compressed',
                            'multipart/x-zip',
                        ]),
                ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return static::$resource::getWidgets();
    }
}
