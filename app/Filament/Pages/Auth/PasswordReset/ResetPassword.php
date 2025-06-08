<?php

namespace App\Filament\Pages\Auth\PasswordReset;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPassword extends BaseResetPassword
{
    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            // ->rule('min:4')
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/password-reset/reset-password.form.password.validation_attribute'));
    }

    /**
     * @override
     * @return PasswordResetResponse|null
     * @throws ValidationException
     */
    public function resetPassword(): ?PasswordResetResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $this->validate([
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&^]/',
                'same:passwordConfirmation',
            ],
            'passwordConfirmation' => [
                'required',
            ],
        ], [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one lowercase letter (a-z), one uppercase letter (A-Z), one number (0-9), and one special character (*, &, ^, etc.).',
            'password.same' => 'Password and confirmation password must match.',
            'passwordConfirmation.required' => 'Password confirmation is required.',
        ]);

        $data = $this->form->getState();
        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $status = Password::broker(Filament::getAuthPasswordBroker())->reset(
            $data,
            function (CanResetPassword | Model $user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title(__($status))
                ->success()
                ->send();

            return app(PasswordResetResponse::class);
        }

        Notification::make()
            ->title(__($status))
            ->danger()
            ->send();

        return null;
    }
}
