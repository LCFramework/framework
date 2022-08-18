<?php

namespace LCFramework\Framework\Auth\Http\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use LCFramework\Framework\Form\Builder\FormBuilder;
use LCFramework\Framework\Support\Auth;
use LCFramework\Framework\Transformer\Facade\Transformer;
use Livewire\Component;

class Register extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public $username = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public function mount(): void
    {
        if (auth()->check()) {
            redirect()->intended();
        }

        $this->form->fill();
    }

    public function render(): View
    {
        return view('lcframework::livewire.auth.register');
    }

    public function submit()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => __('filament::register.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        Auth::register($data);

        return redirect('/');
    }

    protected function getFormSchema(): array
    {
        return Transformer::transform(
            'register.form',
            FormBuilder::make()
                ->schema([
                    Placeholder::make('register_link')
                        ->view('lcframework::components.auth.login-link'),
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->unique('users')
                        ->maxLength(30),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->autocomplete()
                        ->unique('users')
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required()
                        ->rules('confirmed'),
                    TextInput::make('password_confirmation')
                        ->label('Confirm password')
                        ->password()
                        ->required(),
                ])
        )->build();
    }
}
