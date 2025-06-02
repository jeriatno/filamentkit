<?php

namespace App\Filament\Resources\RentInResource\Pages;

use App\Filament\Resources\RentInResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentIn extends EditRecord
{
    protected static string $resource = RentInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
