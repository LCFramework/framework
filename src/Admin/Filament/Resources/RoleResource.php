<?php

namespace LCFramework\Framework\Admin\Filament\Resources;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use LCFramework\Framework\Admin\Filament\Resources\RoleResource\Pages\CreateRole;
use LCFramework\Framework\Admin\Filament\Resources\RoleResource\Pages\EditRole;
use LCFramework\Framework\Admin\Filament\Resources\RoleResource\Pages\ListRoles;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = 'Roles';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Hidden::make('guard')
                            ->dehydrateStateUsing(fn () => 'web'),
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),

                Card::make()
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?Role $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                        Placeholder::make('updated_at')
                            ->label('Updated at')
                            ->content(fn (?Role $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
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
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
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
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(function (Role $record): bool {
                        return $record->id === 1 || $record->id === 2;
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return $record->id !== 1 && $record->id !== 2;
    }
}
