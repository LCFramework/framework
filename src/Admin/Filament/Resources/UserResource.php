<?php

namespace LCFramework\Framework\Admin\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages\ListUsers;
use LCFramework\Framework\Auth\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->label('Username')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->sortable()
                    ->searchable(),
                BooleanColumn::make('email_verified_at')
                    ->label('Verified')
                    ->sortable()
                    ->getStateUsing(fn(User $record): bool => $record->hasVerifiedEmail()),
                TextColumn::make('create_date')
                    ->label('Created at')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('update_time')
                    ->label('Updated at')
                    ->date()
                    ->sortable()
                    ->searchable()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }
}
