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
    protected ?int $roleId;

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
        $data['password'] = Hash::make('password');
        $this->roleId = $data['role_id'];
        return $data;
    }

    protected function afterCreate(): void
    {
        $role = Role::find($this->roleId);
        $this->record->assignRole($role->name);
    }
}
