<?php

namespace App\Filament\Resources\Master\RackResource\Pages;

use App\Filament\Resources\Master\RackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRack extends EditRecord
{
    protected static string $resource = RackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
