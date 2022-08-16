<?php

namespace LCFramework\Framework\Installer\Http\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use LCFramework\Framework\LCFramework;
use Livewire\Component;

class Installer extends Component implements HasForms
{
    use InteractsWithForms;

    public function mount(): void
    {
        if (LCFramework::installed()) {
            redirect()->intended();
        }

        $this->form->fill();
    }

    public function render(): View
    {
        return view('lcframework::livewire.installer.index');
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make('Requirements')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                    ]),
                Wizard\Step::make('Application Settings')
                    ->schema([]),
                Wizard\Step::make('Database Settings')
                    ->schema([]),
                Wizard\Step::make('LastChaos Settings')
                    ->schema([]),
                Wizard\Step::make('Email Settings')
                    ->schema([]),
            ]),
        ];
    }
}
