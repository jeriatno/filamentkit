<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use App\Filament\Resources\CustomerResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user.pages.view';

    public function getTitle(): string
    {
        return 'User Detail';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
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
