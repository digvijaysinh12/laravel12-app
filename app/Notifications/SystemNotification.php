<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SystemNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $type,
        protected string $title,
        protected string $message,
        protected string $audience,
        protected string $actionUrl,
        protected string $icon = 'system',
    ) {}

    /**
     * Get the notification delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Store notification in database.
     */
    public function toDatabase(object $notifiable): DatabaseMessage|array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'audience' => $this->audience,
            'action_url' => $this->actionUrl,
            'icon' => $this->icon,
        ];
    }

    /**
     * Array representation.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'audience' => $this->audience,
            'action_url' => $this->actionUrl,
            'icon' => $this->icon,
        ];
    }
}