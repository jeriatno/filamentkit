<?php

namespace App\Filament\Resources\Goods\OutcomingGoodsResource\Pages;

use App\Filament\Resources\Goods\OutcomingGoodsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOutcomingGoods extends EditRecord
{
    protected static string $resource = OutcomingGoodsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
