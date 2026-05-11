<?php

namespace App\Services;

use App\Models\Order;
use App\Support\Notifications\AdminRecipientResolver;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    public function __construct(
        private readonly AdminRecipientResolver $adminRecipientResolver,
    ) {}

    public function notifyOrderShipped(Order $order): void
    {
        $order->loadMissing('user');

        if (! $order->user) {
            Log::warning('Order shipped notification skipped because the order user is missing.', [
                'order_id' => $order->id,
            ]);

            return;
        }

        $order->user->notify(new \App\Notifications\OrderShipped($order));
    }

    public function notifyAdmins(Notification $notification, array $context = []): void
    {
        $admins = $this->adminRecipientResolver->getAdmins();

        if ($admins->isEmpty()) {
            Log::channel('notification')->warning('Admin notification skipped because no admin users were found.', $context + [
                'notification' => $notification::class,
            ]);

            return;
        }

        NotificationFacade::send($admins, $notification);

        Log::channel('notification')->info('Admin notification dispatched.', $context + [
            'notification' => $notification::class,
            'admin_count' => $admins->count(),
        ]);
    }

    public function sendOnDemand(string $email, Notification $notification, array $context = []): void
    {
        NotificationFacade::route('mail', $email)->notify($notification);

        Log::info('On-demand notification dispatched.', $context + [
            'notification' => $notification::class,
            'recipient' => $email,
        ]);
    }

    public function sendAnonymousAdminAlert(string $email, Notification $notification, array $context = []): void
    {
        $anonymous = new AnonymousNotifiable;
        $anonymous->route('mail', $email);

        $anonymous->notify($notification);

        Log::info('Anonymous admin alert dispatched.', $context + [
            'notification' => $notification::class,
            'recipient' => $email,
        ]);
    }
}
