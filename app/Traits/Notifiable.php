<?php

namespace App\Traits;

use App\Http\Responses\BaseResponse;
use App\Models\User\User;
use Filament\Notifications\Notification;

trait Notifiable
{
    /**
     * Show a "Nothing has been changed" notification
     */
    protected function sendNoChangesNotification(): void
    {
        $resourceName = $this->getResourceName();
        BaseResponse::noChanges($resourceName);
    }

    /**
     * Show a success notification
     */
    protected function sendSuccessNotification($action): void
    {
        $resourceName = $this->getResourceName();
        BaseResponse::view($resourceName, $action);
    }

    /**
     * Show a fail notification
     */
    protected function sendFailNotification($e, $action): void
    {
        BaseResponse::view($e, $action);
    }

    /**
     * Show a info notification
     */
    protected function sendInfoNotification($title, $message): void
    {
        BaseResponse::info($title, $message);
    }

    /**
     * Show a warning notification
     */
    protected function sendWarnNotification($title, $message): void
    {
        BaseResponse::warn($title, $message);
    }

    /**
     * Show a error notification
     */
    protected function sendErrorNotification($message): void
    {
        BaseResponse::error($message);
    }

    /**
     * Get the resource name from the model
     */
    protected function getResourceName(): string
    {
        $resourceName = class_basename(static::getResource());
        $resourceName = str_replace('Resource', '', $resourceName);
        return trim(preg_replace('/([a-z])([A-Z])/', '$1 $2', $resourceName));
    }

    /**
     * Check if any changes were made to the record
     */
    protected function isRecordDirty(array $newData, array $relationMapping = []): bool
    {
        foreach ($newData as $key => $value) {
            if (isset($relationMapping[$key])) {
                $relationName = $relationMapping[$key];
                $relationValue = $this->record->{$relationName};

                if ($relationValue && $relationValue->{$key} !== $value) {
                    return true;
                }
            } else {
                if ($this->record->getAttribute($key) !== $value) {
                    return true;
                }
            }

        }

        return false;
    }

    /**
     * Override Filament's default saved notification
     */
    protected function getSavedNotification(): ?Notification
    {
        $this->sendSuccessNotification('saved');
        return null;
    }

    /**
     * Override Filament's default created notification
     */
    protected function getCreatedNotification(): ?Notification
    {
        $this->sendSuccessNotification('created');
        return null;
    }

    /**
     * Override Filament's default failed notification
     */
    protected function getFailedNotification($e, $action): ?Notification
    {
        $this->sendFailNotification($e, $action);
        return null;
    }
}