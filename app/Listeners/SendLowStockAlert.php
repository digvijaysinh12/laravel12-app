<?php

namespace App\Listeners;

use App\Events\ProductStockLow;
use App\Mail\LowStockAlert;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlert
{
    public function handle(ProductStockLow $event): void
    {
        $product = $event->product;
        $throttleKey = 'mail.low-stock-alert.'.$product->id;

        if (Cache::has($throttleKey)) {
            Log::channel('mail')->info('Low stock alert throttled.', [
                'product_id' => $product->id,
            ]);

            return;
        }

        $recipients = config('mail.admin_recipients');
        $to = $recipients['to'] ?? [];

        if ($to === []) {
            Log::channel('mail')->warning('Low stock alert skipped because no admin recipients were configured.', [
                'product_id' => $product->id,
            ]);

            return;
        }

        Cache::remember($throttleKey, now()->addHour(), fn () => true);

        Mail::to($to)
            ->cc($recipients['cc'] ?? [])
            ->bcc($recipients['bcc'] ?? [])
            ->queue(new LowStockAlert(collect([$product])));
    }
}
