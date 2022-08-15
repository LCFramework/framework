<?php

namespace LCFramework\Framework\Auth\Http\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use LCFramework\Framework\Auth\Contracts\ShouldVerifyEmail;
use LCFramework\Framework\Form\Builder\FormBuilder;
use LCFramework\Framework\Transformer\Facade\Transformer;
use Livewire\Component;

class EmailVerification extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public function mount(): void
    {
        if (! auth()->check()) {
            redirect()->intended(route('login'));
        }

        $user = auth()->user();
        if (
            $user->hasVerifiedEmail() ||
            ($user instanceof ShouldVerifyEmail && ! $user->shouldVerifyEmail())
        ) {
            redirect()->intended();
        }

        $this->form->fill([
            'email' => $user->email,
        ]);
    }

    public function render(): View
    {
        return view('lcframework::livewire.auth.email-verification');
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

        auth()->user()->sendEmailVerificationNotification();

        Notification::make()
            ->success()
            ->title('Email has been successfully sent')
            ->send();
    }

    protected function getFormSchema(): array
    {
        return Transformer::transform(
            'email-verification.form',
            FormBuilder::make()
                ->schema([
                    TextInput::make('email')
                        ->label('Email address')
                        ->disabled(),
                ])
        )->build();
    }
}
