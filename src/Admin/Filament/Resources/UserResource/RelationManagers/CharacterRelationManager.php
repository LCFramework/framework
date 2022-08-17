<?php

namespace LCFramework\Framework\Admin\Filament\Resources\UserResource\RelationManagers;

use Closure;
use DateTimeInterface;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
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
                    ->options(fn(Character $record) => CharacterJob::get($record->a_job))
                    ->required(),
                Toggle::make('a_admin')
                    ->label('Administrator')
                    ->helperText('Grant access to in-game admin commands')
                    ->afterStateHydrated(
                        function (Toggle $component, Character $record): void {
                            $component->state($record->is_admin);
                        }
                    )
                    ->dehydrated(function (bool $state, ?Character $record): bool {
                        if ($record === null) {
                            return true;
                        }

                        $isAdmin = $record->is_admin;

                        return $state ? !$isAdmin : $isAdmin;
                    })
                    ->dehydrateStateUsing(fn(bool $state): int => $state ? 10 : 0),
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
                BooleanColumn::make('a_admin')
                    ->label('Administrator')
                    ->sortable()
                    ->getStateUsing(fn(Character $record): bool => $record->is_admin),
                TextColumn::make('a_createdate')
                    ->label('Created at')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('a_deletedelay')
                    ->label('Deleting at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn(Character $record): DateTimeInterface => Date::createFromTimestamp($record->a_deletedelay)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                TernaryFilter::make('pendingDeletion')
                    ->placeholder('Without pending deletes')
                    ->trueLabel('With pending deletes')
                    ->falseLabel('Only pending deletes')
                    ->queries(
                        true: fn(Builder $query) => $query->withPendingDeletes(),
                        false: fn(Builder $query) => $query->onlyPendingDeletes(),
                        blank: fn(Builder $query) => $query->withoutPendingDeletes(),
                    )
                    ->default()
            ]);
    }
}
