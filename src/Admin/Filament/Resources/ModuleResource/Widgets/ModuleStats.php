<?php

namespace LCFramework\Framework\Admin\Filament\Resources\ModuleResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use LCFramework\Framework\Module\Facade\Modules;

class ModuleStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make(
                'Total modules',
                number_format(count(Modules::all()))
            ),
            Card::make(
                'Enabled modules',
                number_format(count(Modules::enabled()))
            ),
            Card::make(
                'Disabled modules',
                number_format(count(Modules::disabled()))
            ),
        ];
    }
}
