<?php

namespace LCFramework\Framework\Auth\Http\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use LCFramework\Framework\Form\Builder\FormBuilder;
use LCFramework\Framework\Transformer\Facade\Transformer;
use Livewire\Component;

class PasswordRequest extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public $email = '';

    public function mount(): void
    {
        if (auth()->check()) {
            redirect()->intended();
        }

        $this->form->fill();
    }

    public function render(): View
    {
        return view('lcframework::livewire.auth.password-request');
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

        $status = Password::sendResetLink($data);

        if ($status === Password::RESET_LINK_SENT) {
            Notification::make()
                ->success()
                ->title(__($status))
                ->body('Please check your inbox (or your spam)')
                ->send();

            return;
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    protected function getFormSchema(): array
    {
        return Transformer::transform(
            'password-request.form',
            FormBuilder::make()
                ->schema([
                    Placeholder::make('register_link')
                        ->view('lcframework::components.auth.remember-password-link'),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->autocomplete(),
                ])
        )->build();
    }
}
