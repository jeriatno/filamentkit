<?php

namespace App\Traits;

use Filament\Actions\Action;

trait ConfirmAction
{
    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(___('action.create.label'))
            ->requiresConfirmation()
            ->modalHeading(___('action.create.heading'))
            ->modalDescription(___('action.create.description'))
            ->modalSubmitActionLabel(___('action.create.submit'))
            ->modalCancelActionLabel(___('action.create.cancel'))
            ->action(fn () => $this->create());
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(___('action.edit.label'))
            ->requiresConfirmation()
            ->modalHeading(___('action.edit.heading'))
            ->modalDescription(___('action.edit.description'))
            ->modalSubmitActionLabel(___('action.edit.submit'))
            ->modalCancelActionLabel(___('action.edit.cancel'))
            ->action(fn () => $this->save());
    }
}