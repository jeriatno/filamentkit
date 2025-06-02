<?php

namespace App\Filament\Resources\Master\PartnerResource\Pages;

use App\Enums\Access;
use App\Filament\Resources\Master\PartnerResource;
use App\Models\User\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;
    protected static ?string $title = 'New Partner';

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
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('12345678'),
        ]);

        // Assign role to user
        $data['user_id'] = $user->id;
        $user->assignRole(Access::PARTNER);

        unset($data['email']);

        return $data;
    }
}
