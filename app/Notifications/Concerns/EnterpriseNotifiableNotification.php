<?php

namespace App\Notifications\Concerns;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;

abstract class EnterpriseNotifiableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    /**
     * Queue split keeps email latency isolated from realtime delivery.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail-notifications',
            'broadcast' => 'realtime-notifications',
            'database' => 'default',
            'webhook' => 'realtime-notifications',
        ];
    }

    public function middleware(object $notifiable, string $channel): array
    {
        if (! method_exists($notifiable, 'getKey')) {
            return [];
        }

        return [
            new RateLimited('notifications:'.$notifiable->getKey()),
        ];
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('stack')->error('Queued notification failed.', [
            'notification' => static::class,
            'error' => $exception->getMessage(),
        ]);
    }
}
