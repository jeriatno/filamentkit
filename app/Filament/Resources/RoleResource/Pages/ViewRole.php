<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament.resources.role.pages.view';

    public function getTitle(): string
    {
        return 'Role Detail';
    }

    public function getFooterActions(): array
    {
        return [
            EditAction::make()
                ->url(fn() => static::getResource()::getUrl('edit', ['record' => $this->record->getKey()])),

            Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
