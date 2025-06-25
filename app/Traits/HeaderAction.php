<?php

namespace App\Traits;

use Filament\Actions;

trait HeaderAction
{
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus')
                ->iconSize('w-4 h-4'),
        ];
    }
}