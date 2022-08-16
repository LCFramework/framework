<?php

namespace LCFramework\Framework\Installer\Http\Livewire;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use LCFramework\Framework\LCFramework;
use Livewire\Component;

class Installer extends Component implements HasForms
{
    use InteractsWithForms;

    public array $extensions = [];

    public function mount(): void
    {
        if (LCFramework::installed()) {
            redirect()->intended();
        }

        $this->form->fill();

        $this->extensions = $this->checkExtensions();
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
                        Placeholder::make('requirements')
                            ->view(
                                'lcframework::components.installer.requirements.introduction'
                            ),
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

    protected function checkExtensions(): array
    {
        $requirements = [
            'php' => true,
            'bcmath' => false,
            'ctype' => false,
            'curl' => false,
            'dom' => false,
            'fileinfo' => false,
            'json' => false,
            'mbstring' => false,
            'openssl' => false,
            'pcre' => false,
            'pdo' => false,
            'pdo_mysql' => false,
            'pdo_sqlite' => false,
            'tokenizer' => false,
            'xml' => false,
        ];

        $extensions = get_loaded_extensions();

        foreach ($extensions as $extension) {
            if (isset($requirements[$extension])) {
                $requirements[$extension] = true;
            }
        }

        return $requirements;
    }
}
