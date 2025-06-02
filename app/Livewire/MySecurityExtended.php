<?php

namespace App\Livewire;

use App\Models\User\User;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Page;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

use function Filament\Support\is_app_url;

class MySecurityExtended extends Page implements HasForms, HasActions
{

    use InteractsWithActions;
    use InteractsWithForms;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public $user;

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
    }

    protected function fillForm(): void
    {
        $data = $this->getUser()->attributesToArray();
        $this->form->fill($data);
    }

    public function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Change Password')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('current_password')
                                    ->label('Previous Password')
                                    ->placeholder('Input previous password')
                                    ->required()
                                    ->password()
                                    ->revealable()
                                    ->rule('current_password')
                                    ->visible(filament('filament-breezy')->getPasswordUpdateRequiresCurrent()),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('new_password')
                                    ->label(__('filament-breezy::default.fields.new_password'))
                                    ->placeholder('Input new password')
                                    ->password()
                                    ->revealable()
                                    ->live()
                                    ->rules(filament('filament-breezy')->getPasswordUpdateRules())
                                    ->minLength(8)
                                    ->rule(function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            if (!preg_match('/[a-z]/', $value)) {
                                                $fail('Password must contain at least one lowercase letter (a-z).');
                                            }

                                            if (!preg_match('/[A-Z]/', $value)) {
                                                $fail('Password must contain at least one uppercase letter (A-Z).');
                                            }

                                            if (!preg_match('/[0-9]/', $value)) {
                                                $fail('Password must contain at least one number (0-9).');
                                            }

                                            if (!preg_match('/[^A-Za-z0-9]/', $value)) {
                                                $fail('Password must contain at least one special character (*, &, ^, etc.).');
                                            }

                                            if (Hash::check($value, auth()->user()->password)) {
                                                $fail('New password must be different from the current password.');
                                            }
                                        };
                                    })
                                    ->validationAttribute('password')
                                    ->afterStateUpdated(function (HasForms $livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->required()
                                    ->dehydrateStateUsing(fn(string $state): string => $state),
                                TextInput::make('new_password_confirmation')
                                    ->label(__('filament-breezy::default.fields.new_password_confirmation'))
                                    ->placeholder('Confirm new password')
                                    ->password()
                                    ->revealable()
                                    ->live()
                                    ->same('new_password')
                                    ->required(),
                            ]),
                    ])
            ])
            ->statePath('data');
    }

    public function updatePassword(): Action
    {
        return Action::make('updatePassword')
            ->requiresConfirmation()
            ->modalHeading('Update Confirmation Password')
            ->modalDescription('Are you sure you would like to update password? You will automatically logout from your account and please sign in with your new password.')
            ->modalSubmitActionLabel('Update')
            ->modalCancelActionLabel('Cancel')
            ->label('Save')
            ->action(fn() => $this->submit());
    }


    public function submit()
    {
        $data = collect($this->form->getState())->only('new_password')->all();

        $this->user->update([
            'password' => Hash::make($data['new_password']),
            'password_changed_at' => now(),
        ]);

        session()->forget('password_hash_' . Filament::getCurrentPanel()->getAuthGuard());

        Notification::make()
            ->success()
            ->title(__('filament-breezy::default.profile.password.notify'))
            ->send();

        // Logout user
        Filament::auth()->logout();

        $this->redirect(Filament::getLoginUrl());
    }

    public function cancel()
    {
        $this->redirect('my-profile');
    }


    public function render(): View
    {
        return view('livewire.my-security-extended');
    }
}
