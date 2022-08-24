<?php

namespace LCFramework\Framework\Admin\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use LCFramework\Framework\Admin\Filament\Resources\ThemeResource\Pages\ListThemes;
use LCFramework\Framework\Admin\Filament\Resources\ThemeResource\Widgets\ThemeStats;
use LCFramework\Framework\Theme\Models\Theme;

class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static ?string $slug = 'appearance/themes';

    protected static ?string $navigationGroup = 'Appearance';

    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                BooleanColumn::make('enabled')
                    ->label('Enabled'),
            ])
            ->filters([
                TernaryFilter::make('enabled')
                    ->label('Enabled'),
            ])
            ->bulkActions([
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
            'index' => ListThemes::route('/'),
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
            ThemeStats::class,
        ];
    }
}
