<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Log;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class LogNotificationFailed
{
    public function handle(NotificationFailed $event): void
    {
        Log::channel('stack')->error('Notification delivery failed.', [
            'channel' => $event->channel,
            'notification' => $event->notification::class,
            'notifiable_type' => $event->notifiable::class,
            'notifiable_id' => method_exists($event->notifiable, 'getKey') ? $event->notifiable->getKey() : null,
            'data' => $event->data,
        ]);
    }
}
