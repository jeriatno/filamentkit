<?php

namespace App\Filament\Resources\RentBillResource\Pages;

use App\Filament\Resources\RentBillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentBill extends EditRecord
{
    protected static string $resource = RentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
