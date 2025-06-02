<?php

namespace App\Filament\Resources\Master\WarahouseResource\Pages;

use App\Filament\Resources\Master\WarehouseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarehouses extends ListRecords
{
    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus-circle')
                ->iconSize('w-5 h-5'),
        ];
    }
}
