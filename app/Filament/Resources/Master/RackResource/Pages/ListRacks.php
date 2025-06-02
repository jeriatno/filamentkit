<?php

namespace App\Filament\Resources\Master\RackResource\Pages;

use App\Filament\Resources\Master\RackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRacks extends ListRecords
{
    protected static string $resource = RackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus-circle')
                ->iconSize('w-5 h-5'),
        ];
    }
}
