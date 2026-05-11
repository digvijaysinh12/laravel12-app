<?php

namespace App\Support\Notifications;

use Illuminate\Notifications\DatabaseNotification;

class NotificationPayload
{
    /**
     * Normalize database notification rows for Blade views, JSON APIs, and Echo.
     */
    public static function fromDatabaseNotification(DatabaseNotification $notification): array
    {
        $data = $notification->data;

        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'icon' => $data['icon'] ?? 'default',
            'action_url' => $data['action_url'] ?? null,
            'action_label' => $data['action_label'] ?? null,
            'audience' => $data['audience'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'tracking_number' => $data['tracking_number'] ?? null,
            'subject' => $data['subject'] ?? null,
            'meta' => $data['meta'] ?? [],
            'is_read' => $notification->read_at !== null,
            'read_at' => $notification->read_at?->toISOString(),
            'created_at' => $notification->created_at?->toISOString(),
            'created_at_human' => $notification->created_at?->diffForHumans(),
        ];
    }
}
