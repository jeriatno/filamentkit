<?php

namespace App\Filament\Resources\Master\RateResource\Pages;

use App\Filament\Resources\Master\RateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRates extends ListRecords
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus-circle')
                ->iconSize('w-5 h-5'),
        ];
    }
}
