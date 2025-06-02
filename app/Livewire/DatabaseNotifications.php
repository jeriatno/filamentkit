<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;

use Filament\Notifications\Livewire\DatabaseNotifications as BaseDatabaseNotifications;
use Livewire\Attributes\On;

class DatabaseNotifications extends BaseDatabaseNotifications
{
    #[On('notificationClose')]
    public function removeNotification(string $id): void
    {
        $this->getNotificationsQuery()
            ->where('id', $id)
            ->delete();
    }

    #[On('markedNotificationAsRead')]
    public function markNotificationAsRead(string $id): void
    {
        $this->getNotificationsQuery()
            ->where('id', $id)
            ->update(['read_at' => now()]);
    }

    #[On('markedNotificationAsUnread')]
    public function markNotificationAsUnread(string $id): void
    {
        $this->getNotificationsQuery()
            ->where('id', $id)
            ->update(['read_at' => null]);
    }

    public function clearNotifications(): void
    {
        $this->getNotificationsQuery()->delete();
    }

    public function markAllNotificationsAsRead(): void
    {
        $this->getUnreadNotificationsQuery()->update(['read_at' => now()]);
    }

    public function getNotifications(): DatabaseNotificationCollection | Paginator
    {
        if (! $this->isPaginated()) {
            return $this->getNotificationsQuery()->get();
        }

        return $this->getNotificationsQuery()->get();
    }

    public function getNotificationsQuery(): Builder | Relation
    {
        return $this->getUser()->notifications()
            ->whereNotNull('blast_id')
            ->whereNull('scheduled_at');
    }

    public function getUnreadNotificationsQuery(): Builder | Relation
    {
        return $this->getNotificationsQuery()->unread();
    }

    public function getUnreadNotificationsCount(): int
    {
        return $this->getUnreadNotificationsQuery()->count();
    }

    public function getUser(): Model | Authenticatable | null
    {
        return Filament::auth()->user();
    }

    public function getPollingInterval(): ?string
    {
        return Filament::getDatabaseNotificationsPollingInterval();
    }

    public function getTrigger(): View
    {
        return view('filament-panels::components.topbar.database-notifications-trigger');
    }

    public function getNotification(DatabaseNotification $notification): Notification
    {
        return Notification::fromDatabase($notification)
            ->date($this->formatNotificationDate($notification->getAttributeValue('created_at')));
    }

    public function render(): View
    {
        return view('filament-notifications::database-notifications');
    }
}
