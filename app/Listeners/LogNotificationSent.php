<?php

namespace App\Listeners;

use App\Support\Notifications\NotificationCache;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

class LogNotificationSent
{
    public function handle(NotificationSent $event): void
    {
        if (method_exists($event->notifiable, 'getKey')) {
            NotificationCache::forgetFor($event->notifiable);
        }

        Log::channel('stack')->info('Notification sent.', [
            'channel' => $event->channel,
            'notification' => $event->notification::class,
            'notifiable_type' => $event->notifiable::class,
            'notifiable_id' => method_exists($event->notifiable, 'getKey') ? $event->notifiable->getKey() : null,
            'response' => $event->response,
        ]);
    }
}
