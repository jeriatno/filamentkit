<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Models\User\User;
use App\Notifications\VerifyEmailNotification;
use App\Settings\MailSettings;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'New User';

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Save')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make('12345678');
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->load('roles');
        if ($this->record->role->name === superadmin()) {
            // Assign superadmin
            $this->assignSuperAdminRole($this->record);
        } else {
            // Assign role to user
            $this->record->assignRole($this->record->name);
        }
    }

    public static function assignSuperAdminRole(User $user)
    {
        $existingSuperAdmin = User::role(superadmin())->exists();

        if (!$existingSuperAdmin) {
            $role = Role::where('name', superadmin())->first();
            if ($role) {
                $user->assignRole($role->name);

                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }

                if (!$user->hasChangedPassword()) {
                    $user->markPasswordAsChanged();
                }
            }
        }
    }
}
