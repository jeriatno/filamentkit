<?php

namespace App\Filament\Resources\Master\RackResource\Pages;

use App\Filament\Resources\Master\RackResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateRack extends CreateRecord
{
    protected static string $resource = RackResource::class;
    protected static ?string $title = 'New Rack';

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
