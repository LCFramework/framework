<?php

namespace LCFramework\Framework\Installer\Http\Livewire;

use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use LCFramework\Framework\Auth\Models\User;
use LCFramework\Framework\LCFramework;
use LCFramework\Framework\Support\Env;
use Livewire\Component;

class Installer extends Component implements HasForms
{
    use InteractsWithForms;

    public array $extensions = [];

    public string $exceptionMessage = '';

    protected $listeners = [
        'openExceptionModal' => 'openExceptionModal',
    ];

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

    public function submit()
    {
        if (LCFramework::installed()) {
            return redirect()->intended();
        }

        $data = $this->form->getState();

        if (! $this->updateEnv($data)) {
            Notification::make()
                ->danger()
                ->title('Settings have failed to update')
                ->body('LCFramework may not have write access to the .env file')
                ->send();

            return;
        }

        if (! $this->updateConfig($data)) {
            Notification::make()
                ->danger()
                ->title('Config has failed to update')
                ->actions([
                    Action::make('exception_message')
                        ->label('View')
                        ->button()
                        ->emit('openExceptionModal'),
                ])
                ->send();

            return;
        }

        if (! $this->runMigrations()) {
            Notification::make()
                ->danger()
                ->title('Migrations have failed to run')
                ->body('LCFramework may not be able to connect to your database')
                ->send();

            return;
        }

        if (! $this->createUser($data)) {
            Notification::make()
                ->danger()
                ->title('Failed to create the user')
                ->body('LCFramework may not be able to connect to the database')
                ->send();

            return;
        }

        Storage::put('lcframework', '');

        Notification::make()
            ->success()
            ->title('Successfully installed')
            ->send();
    }

    public function openExceptionModal(): void
    {
        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'exception-modal',
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make()
                ->submitAction(view('lcframework::components.installer.submit'))
                ->schema([
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
                                        ->rules('confirmed'),
                                    TextInput::make('db_password_confirmation')
                                        ->label('Confirm password')
                                        ->password(),
                                    TextInput::make('db_name')
                                        ->label('Database name')
                                        ->required(),
                                    TextInput::make('db_port')
                                        ->label('Port')
                                        ->default(3306)
                                        ->required()
                                        ->integer()
                                        ->minValue(0),
                                ]),
                        ]),
                    Wizard\Step::make('LastChaos Settings')
                        ->schema([
                            Select::make('lc_version')
                                ->label('Version')
                                ->required()
                                ->options([
                                    4 => 'Version 4',
                                ]),
                            Grid::make()
                                ->columns([
                                    'sm' => 2,
                                ])
                                ->schema([
                                    Select::make('lc_hash')
                                        ->label('Password hash')
                                        ->required()
                                        ->options([
                                            'sha256' => 'SHA-256',
                                            'md5' => 'MD5',
                                            'plaintext' => 'Text',
                                        ]),
                                    TextInput::make('lc_salt')
                                        ->label('Salt')
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
                                        ->password()
                                        ->dehydrated(fn ($state) => filled($state)),
                                    TextInput::make('mail_from_address')
                                        ->label('From address')
                                        ->hint('The sender email address'),
                                    TextInput::make('mail_from_name')
                                        ->label('From name')
                                        ->hint('The sender name')
                                        ->helperText(new HtmlString('Use <code>${APP_NAME}</code> to send the application name')),
                                    TextInput::make('mail_port')
                                        ->label('Port')
                                        ->integer()
                                        ->minValue(0),
                                    Select::make('mail_encryption')
                                        ->label('Encryption')
                                        ->options([
                                            'tls' => 'TLS',
                                        ]),
                                ]),
                        ]),
                    Wizard\Step::make('Administrator')
                        ->schema([
                            Grid::make()
                                ->columns([
                                    'sm' => 2,
                                ])
                                ->schema([
                                    TextInput::make('user_username')
                                        ->label('Username')
                                        ->required()
                                        ->maxLength(30),
                                    TextInput::make('user_email')
                                        ->label('Email address')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('user_password')
                                        ->label('Password')
                                        ->password()
                                        ->required()
                                        ->rules('confirmed'),
                                    TextInput::make('user_password_confirmation')
                                        ->label('Confirm password')
                                        ->password()
                                        ->required(),
                                ]),
                        ]),
                ]),
        ];
    }

    protected function updateEnv(array $data): bool
    {
        Env::make()
            ->put('APP_NAME', $data['app_name'] ?? '')
            ->put('APP_URL', $data['app_url'] ?? '')
            ->put('APP_ENV', $data['app_environment'] ?? '')
            ->put('APP_DEBUG', $data['app_debug'] ?? '')
            ->put('LCFRAMEWORK_LAST_CHAOS_VERSION', $data['lc_version'] ?? '')
            ->put('LCFRAMEWORK_LAST_CHAOS_AUTH_SALT', $data['lc_salt'] ?? '')
            ->put('LCFRAMEWORK_LAST_CHAOS_AUTH_HASH', $data['lc_hash'] ?? '')
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_DATA', $data['lc_db_data'] ?? '')
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_DB', $data['lc_db_db'] ?? '')
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_AUTH', $data['lc_db_auth'] ?? '')
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_POST', $data['lc_db_post'] ?? '')
            ->put('DB_HOST', $data['db_host'] ?? '')
            ->put('DB_PORT', $data['db_port'] ?? '')
            ->put('DB_DATABASE', $data['db_name'] ?? '')
            ->put('DB_USERNAME', $data['db_username'] ?? '')
            ->put('DB_PASSWORD', $data['db_password'] ?? '')
            ->put('MAIL_HOST', $data['mail_host'] ?? '')
            ->put('MAIL_PORT', $data['mail_port'] ?? '')
            ->put('MAIL_USERNAME', $data['mail_username'] ?? '')
            ->put('MAIL_PASSWORD', $data['mail_password'] ?? '')
            ->put('MAIL_ENCRYPTION', $data['mail_encryption'] ?? '')
            ->put('MAIL_FROM_ADDRESS', $data['mail_from_address'] ?? '')
            ->put('MAIL_FROM_NAME', $data['mail_from_name'] ?? '')
            ->save();

        return true;
    }

    protected function updateConfig(array $data): bool
    {
        try {
            config([
                'app.url' => $data['app_url'],
                'app.env' => $data['app_environment'],
                'app.debug' => $data['app_debug'],
                'lcframework.last_chaos.auth.hash' => $data['lc_hash'],
                'lcframework.last_chaos.auth.salt' => $data['lc_salt'],
                'lcframework.last_chaos.database.data' => $data['lc_db_data'],
                'lcframework.last_chaos.database.db' => $data['lc_db_db'],
                'lcframework.last_chaos.database.auth' => $data['lc_db_auth'],
                'lcframework.last_chaos.database.post' => $data['lc_db_post'],
                'database.connections.mysql' => [
                    'driver' => 'mysql',
                    'url' => null,
                    'host' => $data['db_host'],
                    'port' => $data['db_port'],
                    'database' => $data['db_name'],
                    'username' => $data['db_username'],
                    'password' => $data['password'],
                    'unix_socket' => '',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                    'options' => [],
                ],
                'mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'host' => $data['mail_host'],
                    'port' => $data['mail_port'],
                    'encryption' => $data['mail_encryption'],
                    'username' => $data['mail_username'],
                    'password' => $data['mail_password'],
                    'timeout' => null,
                    'local_domain' => null,
                ],
                'mail.address' => [
                    'address' => $data['mail_from_address'],
                    'name' => $data['mail_from_name'],
                ],
            ]);

            return true;
        } catch (Exception $e) {
            $this->exceptionMessage = $e->getMessage();

            return false;
        }
    }

    protected function runMigrations(): bool
    {
        try {
            Artisan::call('migrate');

            return true;
        } catch (Exception $e) {
            $this->exceptionMessage = $e->getMessage();

            return false;
        }
    }

    protected function createUser(array $data): bool
    {
        try {
            User::query()
                ->create([
                    'user_id' => $data['user_username'],
                    'email' => $data['user_email'],
                    'password' => Hash::make($data['user_password']),
                    'email_verified_at',
                ]);

            return true;
        } catch (Exception $e) {
            $this->exceptionMessage = $e->getMessage();

            return false;
        }
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
