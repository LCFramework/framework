<?php

namespace LCFramework\Framework\Admin\Filament\Resources\ThemeResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;
use LCFramework\Framework\Admin\Filament\Resources\ThemeResource;
use LCFramework\Framework\Theme\Facade\Themes;
use LCFramework\Framework\Theme\Models\Theme;

class ListThemes extends ListRecords
{
    protected static string $resource = ThemeResource::class;

    public function enableTheme(Theme $record): void
    {
        Themes::enable($record->name);

        $record->forceFill(['enabled' => true])->save();

        Notification::make()
            ->success()
            ->title(sprintf('Theme "%s" has been successfully enabled', $record->name))
            ->body(fn() => $record !== null ? 'This includes the parent theme' : null)
            ->send();
    }

    public function disableTheme(Theme $record): void
    {
        Themes::disable($record->name);

        $record->forceFill(['enabled' => false])->save();

        Notification::make()
            ->success()
            ->title(sprintf('Theme "%s" has been successfully disabled', $record->name))
            ->body(fn() => $record !== null ? 'This includes the parent theme' : null)
            ->send();
    }

    public function deleteTheme(Theme $record): void
    {
        if (Themes::delete($record->name)) {
            $record->delete();

            Notification::make()
                ->success()
                ->title(sprintf('Theme "%s" has been successfully deleted', $record->name))
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title(sprintf('Theme "%s" has been unsuccessfully deleted', $record->name))
                ->body('LCFramework may not have writable permissions to the theme directory')
                ->send();
        }
    }

    public function deleteBulk(Collection $records): void
    {
        $count = 0;
        foreach ($records as $theme) {
            if (!Themes::delete($theme->name)) {
                Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to delete theme "%s"',
                            $theme->name
                        )
                    )
                    ->body('LCFramework may not have writable permissions to the theme directory')
                    ->send();

                continue;
            }

            $theme->delete();

            $count++;
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s themes have been successfully deleted',
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
                ->hidden(fn(Theme $record): bool => $record->enabled)
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->action('enableTheme'),
            Action::make('disable')
                ->label('Disable')
                ->button()
                ->hidden(fn(Theme $record): bool => !$record->enabled)
                ->icon('heroicon-o-x')
                ->requiresConfirmation()
                ->action('disableTheme'),
            Action::make('delete')
                ->label('Delete')
                ->button()
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action('deleteTheme'),
        ];
    }
}
