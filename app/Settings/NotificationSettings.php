<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public string $new_task;
    public string $new_task_schedule_time;
    public string $task_reminder;
    public string $task_reminder_minutes;
    public string $min_task_reminder_minutes;
    public string $blast_notification;

    public static function group(): string
    {
        return 'notification';
    }

    public function loadNotificationToConfig(): void
    {
        config([
            'notification.new_task' => $this->new_task,
            'notification.new_task_schedule_time' => $this->new_task_schedule_time,
            'notification.task_reminder' => $this->task_reminder,
            'notification.task_reminder_minutes' => $this->task_reminder_minutes,
            'notification.min_task_reminder_minutes' => $this->min_task_reminder_minutes,
            'notification.blast_notification' => $this->blast_notification,
        ]);
    }
}
