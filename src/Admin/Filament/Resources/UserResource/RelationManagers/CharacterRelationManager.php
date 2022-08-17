<?php

namespace LCFramework\Framework\Admin\Filament\Resources\UserResource\RelationManagers;

use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use LCFramework\Framework\LastChaos\Models\Character;
use LCFramework\Framework\LastChaos\Support\CharacterJob;

class CharacterRelationManager extends RelationManager
{
    protected static string $relationship = 'characters';

    protected static ?string $recordTitleAttribute = 'a_nick';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('a_name'),
                TextInput::make('a_nick')
                    ->label('Name')
                    ->required()
                    ->maxLength(20)
                    ->afterStateUpdated(function (string $state, Closure $set): void {
                        $set('a_name', $state);
                    }),
                TextInput::make('a_level')
                    ->label('Level')
                    ->required()
                    ->minValue(1),
                Select::make('a_job2')
                    ->label('Job')
                    ->options(fn (Character $record) => CharacterJob::get($record->a_job))
                    ->required(),
            ]);
    }

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
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}
