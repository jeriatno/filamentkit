<?php

namespace App\Filament\Resources\RentOutResource\Pages;

use App\Filament\Resources\RentOutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentOut extends EditRecord
{
    protected static string $resource = RentOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
