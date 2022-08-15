<?php

namespace LCFramework\Framework\Auth\Http\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use LCFramework\Framework\Form\Builder\FormBuilder;
use LCFramework\Framework\Transformer\Facade\Transformer;
use Livewire\Component;

class Login extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public $email = '';

    public $password = '';

    public $remember = false;

    public function mount(): void
    {
        if (auth()->check()) {
            redirect()->intended();
        }

        $this->form->fill();
    }

    public function render(): View
    {
        return view('lcframework::livewire.auth.login');
    }

    public function submit()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => __('filament::login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        if (! auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'])) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email address or password',
            ]);
        }

        return redirect('/');
    }

    protected function getFormSchema(): array
    {
        return Transformer::transform(
            'login.form',
            FormBuilder::make()
                ->schema([
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->autocomplete(),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required(),
                    Grid::make()
                        ->schema([
                            Checkbox::make('remember')
                                ->label('Remember me'),
                            Placeholder::make('forgot_password')
                                ->view('lcframework::components.auth.forgot-password-link'),
                        ])
                        ->columns([
                            'default' => 2,
                        ]),
                ])
                ->build()
        );
    }
}
