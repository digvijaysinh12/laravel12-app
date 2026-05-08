<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\EnterpriseNotifiableNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class OrderShipped extends EnterpriseNotifiableNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Order $order,
    ) {
        $this->afterCommit();

        Log::channel('mail')->info('OrderShipped Notification Constructed', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
        ]);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        Log::channel('mail')->info('OrderShipped delivery channels resolved.', [
            'channels' => ['mail', 'database'],
            'notifiable_id' => $notifiable->id ?? null,
        ]);

        return ['mail', 'database'];
    }

    public function withDelay(object $notifiable): array
    {
        return [
            'mail' => now()->addSeconds(5),
            'database' => now(),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::channel('mail')->info('Sending OrderShipped mail notification', [
            'order_id' => $this->order->id,
            'email' => $notifiable->email ?? null,
        ]);

        return (new MailMessage)
            ->subject(__('Your order :number has shipped', ['number' => $this->order->order_number]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? 'Customer']))
            ->line(__('Your order has been shipped.'))
            ->line(__('Tracking Number: :tracking', ['tracking' => $this->order->tracking_number ?: __('Pending assignment')]))
            ->action(__('View Order'), route('user.orders.show', $this->order))
            ->line(__('Thank you for shopping with us!'));
    }

    /**
     * Store notification in database.
     */
    public function toArray(object $notifiable): array
    {
        Log::channel('mail')->info('Persisting OrderShipped database notification.', [
            'order_id' => $this->order->id,
            'user_id' => $notifiable->id ?? null,
        ]);

        return [
            'title' => 'Order Shipped',
            'order_id' => $this->order->id,
            'action_url' => route('user.orders.show', $this->order),
            'action_label' => 'View Order',
            'tracking_number' => $this->order->tracking_number,
            'message' => 'Your order '.$this->order->order_number.' has been shipped.',
            'icon' => 'truck',
            'meta' => [
                'order_number' => $this->order->order_number,
                'status' => $this->order->status,
            ],
        ];
    }
}
