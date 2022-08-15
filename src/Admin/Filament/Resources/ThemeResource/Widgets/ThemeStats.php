<?php

namespace LCFramework\Framework\Admin\Filament\Resources\ThemeResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use LCFramework\Framework\Theme\Facade\Themes;

class ThemeStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make(
                'Total themes',
                number_format(count(Themes::all()))
            ),
            Card::make(
                'Enabled themes',
                Themes::enabled() !== null ? 1 : 0
            ),
            Card::make(
                'Disabled themes',
                number_format(count(Themes::disabled()))
            ),
        ];
    }
}
