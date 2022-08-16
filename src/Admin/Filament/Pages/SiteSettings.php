<?php

namespace LCFramework\Framework\Admin\Filament\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use LCFramework\Framework\Support\Env;

class SiteSettings extends Page
{
    protected static ?string $slug = 'administration/site-settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 9999;

    protected static string $view = 'lcframework::filament.pages.admin.site-settings';

    public function mount(): void
    {
        $this->form->fill($this->loadConfig());
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            if (blank($value)) {
                $data[$key] = '';
            }
        }

        $env = Env::make()
            ->put('APP_NAME', $data['app_name'])
            ->put('APP_URL', $data['app_url'])
            ->put('APP_ENV', $data['app_environment'])
            ->put('APP_DEBUG', $data['app_debug'])
            ->put('APP_DEBUG', $data['app_debug'])
            ->put('LCFRAMEWORK_LAST_CHAOS_VERSION', $data['lc_version'])
            ->put('LCFRAMEWORK_LAST_CHAOS_AUTH_SALT', $data['lc_salt'])
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_DATA', $data['lc_db_data'])
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_DB', $data['lc_db_db'])
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_AUTH', $data['lc_db_auth'])
            ->put('LCFRAMEWORK_LAST_CHAOS_DATABASE_POST', $data['lc_db_post'])
            ->put('MAIL_HOST', $data['mail_host'])
            ->put('MAIL_PORT', $data['mail_port'])
            ->put('MAIL_USERNAME', $data['mail_username'])
            ->put('MAIL_ENCRYPTION', $data['mail_encryption'])
            ->put('MAIL_FROM_ADDRESS', $data['mail_from_address'])
            ->put('MAIL_FROM_NAME', $data['mail_from_name']);

        $mailPassword = $data['mail_password'];
        if (! blank($mailPassword)) {
            $env->put('MAIL_PASSWORD', $mailPassword);
        }

        $dbPassword = $data['db_password'];
        if (! blank($dbPassword)) {
            $env->put('DB_PASSWORD', $dbPassword);
        }

        if ($env->save()) {
            Notification::make()
                ->success()
                ->title('Settings have been successfully updated')
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Settings have failed to update')
                ->body('LCFramework may not have write access to the .env file')
                ->send();
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Application Settings')
                ->description('Global application settings')
                ->collapsible()
                ->collapsed()
                ->columns([
                    'sm' => 2,
                ])
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
            Section::make('LastChaos Settings')
                ->description('Your LastChaos server settings')
                ->collapsible()
                ->collapsed()
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
            Section::make('Email Settings')
                ->description('Your email server settings')
                ->collapsible()
                ->collapsed()
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
            Section::make('Database Settings')
                ->description('Your database server settings (ensure you know what you\'re doing updating this)')
                ->collapsible()
                ->collapsed()
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
                                ->hint('Leave blank to ignore')
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
                                ->required()
                                ->integer()
                                ->minValue(0),
                        ]),
                ]),
        ];
    }

    protected function loadConfig(): array
    {
        return [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_environment' => config('app.env'),
            'app_debug' => config('app.debug'),

            'lc_version' => config('lcframework.last_chaos.version'),
            'lc_db_data' => config('lcframework.last_chaos.database.auth'),
            'lc_db_db' => config('lcframework.last_chaos.database.db'),
            'lc_db_auth' => config('lcframework.last_chaos.database.auth'),
            'lc_db_post' => config('lcframework.last_chaos.database.post'),

            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),

            'db_host' => config('database.connections.mysql.host'),
            'db_username' => config('database.connections.mysql.username'),
            'db_name' => config('database.connections.mysql.database'),
            'db_port' => config('database.connections.mysql.port'),
        ];
    }
}
