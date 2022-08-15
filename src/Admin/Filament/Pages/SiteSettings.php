<?php

namespace LCFramework\Framework\Admin\Filament\Pages;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
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
                    Grid::make()
                        ->schema([
                            Card::make()
                        ])
                        ->columnSpan([
                            'md' => 2
                        ])
                        ->extraAttributes(['class' => 'col-start-2'])
                ])
                ->columns([
                    'md' => 3,
                    'lg' => null
                ])
        ];
    }
}
