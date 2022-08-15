<?php

namespace LCFramework\Framework\Form;

use Filament\Forms\Components\Component;

class FormServiceProvider
{
    public function boot(): void
    {
        Component::macro('getChildComponentsReference', function &(): array {
            return $this->childComponents;
        });
    }
}
