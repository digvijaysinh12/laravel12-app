<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWebhook')) {
            return;
        }

        $url = $notifiable->routeNotificationFor('webhook', $notification)
            ?? config('services.notifications.webhook.url');

        if (! $url) {
            Log::channel('stack')->warning('Webhook notification skipped because no route was configured.', [
                'notification' => $notification::class,
                'notifiable_type' => $notifiable::class,
                'notifiable_id' => method_exists($notifiable, 'getKey') ? $notifiable->getKey() : null,
            ]);

            return;
        }

        $payload = $notification->toWebhook($notifiable);

        rescue(function () use ($url, $payload, $notification, $notifiable): void {
            Http::timeout((int) config('services.notifications.webhook.timeout', 5))
                ->retry(
                    (int) config('services.notifications.webhook.retries', 3),
                    (int) config('services.notifications.webhook.retry_sleep_ms', 200),
                    throw: false,
                )
                ->acceptJson()
                ->post($url, $payload)
                ->throw();

            Log::channel('stack')->info('Webhook notification delivered.', [
                'notification' => $notification::class,
                'notifiable_type' => $notifiable::class,
                'notifiable_id' => method_exists($notifiable, 'getKey') ? $notifiable->getKey() : null,
                'url' => $url,
            ]);
        }, report: false, rescue: function (\Throwable $exception) use ($url, $payload, $notification, $notifiable): void {
            Log::channel('stack')->error('Webhook notification delivery failed.', [
                'notification' => $notification::class,
                'notifiable_type' => $notifiable::class,
                'notifiable_id' => method_exists($notifiable, 'getKey') ? $notifiable->getKey() : null,
                'url' => $url,
                'payload' => $payload,
                'error' => $exception->getMessage(),
            ]);
        });
    }
}
