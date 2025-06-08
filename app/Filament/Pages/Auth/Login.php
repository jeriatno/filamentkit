<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;

class Login extends BasePage
{
    public ?string $email = '';

    public function mount(): void
    {
        parent::mount();

        session()->put('is_login_page', true);
        $this->email = request('email', '');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                    ->label('Email'),
                $this->getPasswordFormComponent(),
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/login.form.email.label'))
            ->email()
            ->prefixIcon('icon-email')
            ->required()
            ->autocomplete(false)
            ->autofocus()
            ->default($this->email)
            ->live(onBlur: true)
            ->extraInputAttributes(['tabindex' => 1])
            ->afterStateUpdated(function ($state) {
                $this->email = $state;
                $this->dispatch('email-updated', $this->resetPasswordUrl());
            });
    }


    #[Computed]
    public function resetPasswordUrl(): string
    {
        return filament()->getRequestPasswordResetUrl() . '?email=' . e($this->email);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(
                fn() => filament()->hasPasswordReset() ?
                    new HtmlString(Blade::render(
                        '<x-filament::link :href="$resetUrl" tabindex="3">
                            {{ __("filament-panels::pages/auth/login.actions.request_password_reset.label") }}
                        </x-filament::link>',
                        ['resetUrl' => $this->resetPasswordUrl()]
                    )) : null
            )
            ->password()
            ->prefixIcon('icon-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2])
            ->live();
    }
}
