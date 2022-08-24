<?php

namespace LCFramework\Framework\Admin\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\MultiSelectFilter;
use Illuminate\Database\Eloquent\Model;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Pages\ListModules;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Widgets\ModuleStats;
use LCFramework\Framework\Module\Models\Module;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $slug = 'extend/modules';

    protected static ?string $navigationGroup = 'Extend';

    protected static ?string $navigationIcon = 'heroicon-o-puzzle';

    protected static ?int $navigationSort = -9999;

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
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state)))
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
                    ->label('Status')
                    ->options([
                        'enabled' => 'Enabled',
                        'disabled' => 'Disabled',
                    ]),
            ])
            ->bulkActions([
                BulkAction::make('enable')
                    ->label('Enable selected')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action('enableBulk'),
                BulkAction::make('disable')
                    ->label('Disable selected')
                    ->icon('heroicon-o-x')
                    ->requiresConfirmation()
                    ->action('disableBulk'),
                BulkAction::make('delete')
                    ->label('Delete selected')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action('deleteBulk'),
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
        return __(number_format(static::getModel()::count()).' Installed');
    }

    public static function getWidgets(): array
    {
        return [
            ModuleStats::class,
        ];
    }
}
