<?php

namespace App\Services;

use App\Models\SettingNotifications;
use App\User;
use Carbon\Carbon;

class NotificationService
{
    public function sendNotification($type, $notifiableType, $notifiableId, $data)
    {
        $notification = new SettingNotifications([
            'type' => $type,
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
            'data' => json_encode($data),
        ]);

        $notification->save();

        return $notification;
    }

    public function markAsRead($notificationId)
    {
        $notification = SettingNotifications::findOrFail($notificationId);
        $notification->read_at = now();
        $notification->save();

        return $notification;
    }

    public function getAllNotifications($notifiableType, $notifiableId)
    {
        return SettingNotifications::where('notifiable_type', $notifiableType)
            ->where('notifiable_id', $notifiableId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getNotificationFor($type, $for, $limit = 5)
    {
        $notifications = SettingNotifications::where('type', $type)
            ->where('data', 'like', '%"for":"' . $for . '"%')
            ->where('read_at', null)
            ->limit($limit)
            ->latest()
            ->get();

        $formattedNotifications = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true);
            $data['id'] = $notification->id;
            $data['link'] = str_replace('/', '_', $data['link']);
            $data['notifiable'] = User::find($notification->notifiable_id)->name;
            $data['datetime'] = Carbon::parse($notification->created_at)->diffForHumans();
            return $data;
        });

        return $formattedNotifications;
    }
}
