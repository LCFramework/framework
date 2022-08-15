<?php

namespace LCFramework\Framework\Admin\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Pages\ListModules;
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
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state)))
                    ->icons([
                        'heroicon-o-minus-sm',
                        'heroicon-o-x' => 'disabled',
                        'heroicon-o-check' => 'success',
                    ])
                    ->colors([
                        'warning',
                        'danger' => 'disabled',
                        'success' => 'enabled',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
        ];
    }
}
