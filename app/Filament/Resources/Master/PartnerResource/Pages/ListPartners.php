<?php

namespace App\Filament\Resources\Master\PartnerResource\Pages;

use App\Filament\Resources\Master\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus-circle')
                ->iconSize('w-5 h-5'),
        ];
    }
}
