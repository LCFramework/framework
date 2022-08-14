<?php

namespace LCFramework\Framework\Auth\Http\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Login extends Component implements HasForms
{
    use InteractsWithForms;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render(): View
    {
        return view('lcframework::livewire.auth.login');
    }
}
