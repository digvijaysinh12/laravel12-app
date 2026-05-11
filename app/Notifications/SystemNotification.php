<?php

namespace App\Notifications;

use App\Notifications\Concerns\EnterpriseNotifiableNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SystemNotification extends EnterpriseNotifiableNotification
{
    public function __construct(
        public string $type,
        public string $title,
        public string $message,
        public string $audience = 'admin',
        public ?int $userId = null,
        public bool $isRead = false,
        public ?string $actionUrl = null,
        public string $icon = 'default',
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        // FIXED: keep database and broadcast support in one notification.
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    private function payload(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
            'action_url' => $this->actionUrl,
            'user_id' => $this->userId,
            'is_read' => $this->isRead,
            'audience' => $this->audience,
        ];
    }
}
