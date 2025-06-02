<?php

namespace App\Filament\Resources\RentOutResource\Pages;

use App\Filament\Resources\RentOutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentOuts extends ListRecords
{
    protected static string $resource = RentOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus-circle')
                ->label('New Rent Out')
                ->iconSize('w-5 h-5'),
        ];
    }
}
