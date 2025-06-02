<?php

namespace App\Filament\Resources\Master\WarahouseResource\Pages;

use App\Filament\Resources\Master\WarehouseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateWarehouse extends CreateRecord
{
    protected static string $resource = WarehouseResource::class;
    protected static ?string $title = 'New Warehouse';

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
