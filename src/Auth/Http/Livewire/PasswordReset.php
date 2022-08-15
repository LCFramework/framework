<?php

namespace LCFramework\Framework\Auth\Http\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LCFramework\Framework\Form\Builder\FormBuilder;
use LCFramework\Framework\Transformer\Facade\Transformer;
use Livewire\Component;

class PasswordReset extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

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
        return view('lcframework::livewire.auth.password-reset');
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

        $status = Password::reset(
            [
                ...$data,
                'token' => request()->token,
            ],
            function ($user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->success()
                ->title(__($status))
                ->send();

            return redirect()
                ->route('login');
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    protected function getFormSchema(): array
    {
        return Transformer::transform(
            'password-reset.form',
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
