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
            Wizard::make()
                ->schema([
                    Wizard\Step::make('Requirements')
                        ->schema([
                            TextInput::make('name')
                                ->required(),
                        ]),
                ]),
        ];
    }
}
