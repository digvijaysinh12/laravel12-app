<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\WebhookChannel;
use App\Notifications\Concerns\EnterpriseNotifiableNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class NewOrderReceived extends EnterpriseNotifiableNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Order $order,
    ) {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast', WebhookChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info('Preparing NewOrderReceived mail notification.', [
            'order_id' => $this->order->id,
            'admin_id' => $notifiable->id ?? null,
        ]);

        return (new MailMessage)
            ->subject(__('New order received: :number', ['number' => $this->order->order_number]))
            ->greeting(__('Hello Admin,'))
            ->line(__('A new order has been placed by :customer.', ['customer' => $this->order->user?->name ?? 'Customer']))
            ->line(__('Order Number: :number', ['number' => $this->order->order_number]))
            ->line(__('Total Amount: :amount', ['amount' => number_format((float) $this->order->total_amount, 2)]))
            ->action(__('Review Order'), route('admin.orders.show', $this->order))
            ->line(__('Realtime dashboard updates continue through the Laravel broadcast notification channel.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Order Received',
            'message' => ($this->order->user?->name ?? 'Customer').' placed order '.$this->order->order_number.'.',
            'icon' => 'order',
            'order_id' => $this->order->id,
            'action_url' => route('admin.orders.show', $this->order),
            'action_label' => 'Review Order',
            'audience' => 'admin',
            'meta' => [
                'order_number' => $this->order->order_number,
                'total_amount' => (float) $this->order->total_amount,
                'customer_name' => $this->order->user?->name,
            ],
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function toWebhook(object $notifiable): array
    {
        return [
            'event' => 'new_order_received',
            'notification' => static::class,
            'order' => [
                'id' => $this->order->id,
                'number' => $this->order->order_number,
                'status' => $this->order->status,
                'total_amount' => (float) $this->order->total_amount,
            ],
            'customer' => [
                'id' => $this->order->user?->id,
                'name' => $this->order->user?->name,
                'email' => $this->order->user?->email,
            ],
            'sent_at' => now()->toISOString(),
        ];
    }
}
