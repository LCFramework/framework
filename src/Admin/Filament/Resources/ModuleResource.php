<?php

namespace LCFramework\Framework\Admin\Filament\Resources;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\MultiSelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Pages\ListModules;
use LCFramework\Framework\Module\Exception\InvalidModuleEnabled;
use LCFramework\Framework\Module\Facade\Modules;
use LCFramework\Framework\Module\Models\Module;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $slug = 'administration/modules';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';

    protected static ?int $navigationSort = 0;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TagsColumn::make('version')
                    ->label('Version')
                    ->sortable()
                    ->searchable()
                    ->separator(),
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->icons([
                        'heroicon-o-minus-sm',
                        'heroicon-o-x' => 'disabled',
                        'heroicon-o-check' => 'enabled',
                    ])
                    ->colors([
                        'warning',
                        'danger' => 'disabled',
                        'success' => 'enabled',
                    ]),
            ])
            ->filters([
                MultiSelectFilter::make('status')
                    ->options([
                        'enabled' => 'Enabled',
                        'disabled' => 'Disabled'
                    ])
            ])
            ->bulkActions([
                BulkAction::make('enable')
                    ->label('Enable selected')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action('bulkEnable'),
                BulkAction::make('disable')
                    ->label('Disable selected')
                    ->icon('heroicon-o-x')
                    ->requiresConfirmation()
                    ->action('bulkDisable'),
                BulkAction::make('delete')
                    ->label('Delete selected')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action('bulkDelete')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public function bulkEnable(Collection $modules): void
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

    public function bulkDisable(Collection $modules): void
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

    public function bulkDelete(Collection $modules): void
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
}
