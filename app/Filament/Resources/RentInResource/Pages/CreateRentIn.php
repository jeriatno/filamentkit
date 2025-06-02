<?php

namespace App\Filament\Resources\RentInResource\Pages;

use App\Filament\Resources\RentInResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateRentIn extends CreateRecord
{
    protected static string $resource = RentInResource::class;
    protected static ?string $title = 'New Rent In';

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
}
