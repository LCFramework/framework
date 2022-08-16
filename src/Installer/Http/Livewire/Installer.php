<?php

namespace LCFramework\Framework\Installer\Http\Livewire;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
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
                        Checkbox::make('requirements_met')
                            ->label('I confirm the environment requirements are met')
                            ->accepted(),
                    ]),
                Wizard\Step::make('Application Settings')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('app_name')
                                    ->label('Application name')
                                    ->helperText('The display name of the application')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('app_url')
                                    ->label('Application URL')
                                    ->hint('HTTPS is recommended')
                                    ->helperText('The base URL of the application (E.G - https://example.com)')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('app_environment')
                                    ->label('Environment')
                                    ->required()
                                    ->options([
                                        'local' => 'Development',
                                        'staging' => 'Staging',
                                        'production' => 'Production',
                                    ]),
                                Toggle::make('app_debug')
                                    ->label('Verbose logging')
                                    ->hint('This should never be enabled in production')
                                    ->helperText('Display detailed errors and enable debugging functionality'),
                            ]),
                    ]),
                Wizard\Step::make('Database Settings')
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 2,
                            ])
                            ->schema([
                                TextInput::make('db_host')
                                    ->label('Host')
                                    ->required(),
                                TextInput::make('db_username')
                                    ->label('Username')
                                    ->required(),
                                TextInput::make('db_password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->rules('confirmed'),
                                TextInput::make('db_password_confirmation')
                                    ->label('Confirm password')
                                    ->password()
                                    ->required(),
                                TextInput::make('db_name')
                                    ->label('Database name')
                                    ->required(),
                                TextInput::make('db_port')
                                    ->label('Port')
                                    ->required()
                                    ->integer()
                                    ->minValue(0),
                            ]),
                    ]),
                Wizard\Step::make('LastChaos Settings')
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 2,
                            ])
                            ->schema([
                                Select::make('lc_version')
                                    ->label('Version')
                                    ->required()
                                    ->options([
                                        4 => 'Version 4',
                                    ]),
                                TextInput::make('lc_salt')
                                    ->label('Salt')
                                    ->hint('Leave blank to ignore')
                                    ->helperText('This should never be shared with anyone'),
                                TextInput::make('lc_db_data')
                                    ->label('Data database')
                                    ->helperText(new HtmlString('For example, <code>lc_data</code>'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('lc_db_db')
                                    ->label('DB database')
                                    ->helperText(new HtmlString('For example, <code>lc_db</code>'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('lc_db_auth')
                                    ->label('Auth database')
                                    ->helperText(new HtmlString('For example, <code>lc_auth_db</code>'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('lc_db_post')
                                    ->label('Post database')
                                    ->helperText(new HtmlString('For example, <code>lc_post</code>'))
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),
                Wizard\Step::make('Email Settings')
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 2,
                            ])
                            ->schema([
                                TextInput::make('mail_host')
                                    ->label('Host'),
                                TextInput::make('mail_username')
                                    ->label('Username')
                                    ->hint('This is usually your email address'),
                                TextInput::make('mail_password')
                                    ->label('Password')
                                    ->hint('Leave blank to ignore')
                                    ->password(),
                                TextInput::make('mail_from_address')
                                    ->label('From address')
                                    ->hint('The sender email address'),
                                TextInput::make('mail_from_name')
                                    ->label('From name')
                                    ->hint('The sender name')
                                    ->helperText(new HtmlString('Use <code>${APP_NAME}</code> to send the application name')),
                                TextInput::make('mail_port')
                                    ->label('Port')
                                    ->required()
                                    ->integer()
                                    ->minValue(0),
                                Select::make('mail_encryption')
                                    ->label('Encryption')
                                    ->options([
                                        'tls' => 'TLS',
                                    ]),
                            ]),
                    ]),
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
            'PDO' => false,
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
