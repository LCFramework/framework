<?php

namespace LCFramework\Framework\Admin\Filament\Pages;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

class SiteSettings extends Page
{
    protected static ?string $slug = 'administration/site-settings';

    protected static ?string $navigationIcon = 'heroicon-o-cloud-upload';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 9999;

    protected static string $view = 'lcframework::filament.pages.admin.site-settings';

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Card::make()
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                        ])
                        ->columns([
                            'sm' => 2,
                        ])
                        ->columnSpan([
                            'sm' => 2,
                        ]),

                    Card::make()
                        ->schema([])
                        ->columnSpan(1),
                ])
                ->columns([
                    'sm' => 3,
                    'lg' => null,
                ])
        ];
    }
}
