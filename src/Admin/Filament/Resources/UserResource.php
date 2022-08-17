<?php

namespace LCFramework\Framework\Admin\Filament\Resources;

use DateTimeInterface;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages\CreateUser;
use LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages\EditUser;
use LCFramework\Framework\Admin\Filament\Resources\UserResource\Pages\ListUsers;
use LCFramework\Framework\Auth\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('user_id')
                            ->label('Username')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->required()
                            ->email()
                            ->unique()
                            ->maxLength(255),
                        TextInput::make('passwd')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(
                                fn (?string $state, User $record): string => Hash::make($state, ['user_id' => $record->user_id])
                            )
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord),
                        TextInput::make('passwd_confirmation')
                            ->label('Confirm password')
                            ->password(),
                    ])
                    ->columns([
                        'sm' => 2,
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),

                Card::make()
                    ->schema([
                        Toggle::make('email_verified_at')
                            ->label('Verified')
                            ->afterStateHydrated(
                                function (Toggle $component, $state): void {
                                    $component->state($state !== null);
                                }
                            )
                            ->dehydrated(function (bool $state, User $record): bool {
                                $verified = $record->hasVerifiedEmail();

                                return $state ? ! $verified : $verified;
                            })
                            ->dehydrateStateUsing(fn (bool $state): ?DateTimeInterface => $state ? now() : null),
                        Placeholder::make('create_date')
                            ->label('Created at')
                            ->content(fn (?User $record): string => $record?->create_date?->diffForHumans() ?? '-'),
                        Placeholder::make('update_time')
                            ->label('Updated at')
                            ->content(fn (?User $record): string => $record?->update_time?->diffForHumans() ?? '-'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns([
                'sm' => 3,
                'lg' => null,
            ]);
    }

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
                    ->getStateUsing(fn (User $record): bool => $record->hasVerifiedEmail()),
                TextColumn::make('create_date')
                    ->label('Created at')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('update_time')
                    ->label('Updated at')
                    ->date()
                    ->sortable()
                    ->searchable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
