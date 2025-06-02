<?php

namespace App\Filament\Resources\Goods\IncomingGoodsResource\Pages;

use App\Filament\Resources\Goods\IncomingGoodsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIncomingGoods extends EditRecord
{
    protected static string $resource = IncomingGoodsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
