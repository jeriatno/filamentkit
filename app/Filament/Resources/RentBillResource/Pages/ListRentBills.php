<?php

namespace App\Filament\Resources\RentBillResource\Pages;

use App\Filament\Resources\RentBillResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentBills extends ListRecords
{
    protected static string $resource = RentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
