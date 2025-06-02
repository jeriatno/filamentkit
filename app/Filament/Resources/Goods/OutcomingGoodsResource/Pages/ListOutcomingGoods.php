<?php

namespace App\Filament\Resources\Goods\OutcomingGoodsResource\Pages;

use App\Filament\Resources\Goods\OutcomingGoodsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutcomingGoods extends ListRecords
{
    protected static string $resource = OutcomingGoodsResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
