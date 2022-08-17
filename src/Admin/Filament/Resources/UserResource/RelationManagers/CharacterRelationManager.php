<?php

namespace LCFramework\Framework\Admin\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class CharacterRelationManager extends RelationManager
{
    protected static string $relationship = 'characters';

    protected static ?string $recordTitleAttribute = 'a_nick';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('a_nick')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('job_title')
                    ->label('Job'),
                TextColumn::make('a_level')
                    ->label('Level')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('a_createdate')
                    ->label('Created at')
                    ->date()
                    ->sortable()
                    ->searchable(),
            ]);
    }
}
