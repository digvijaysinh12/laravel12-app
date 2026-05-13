<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\WebhookChannel;
use App\Notifications\Concerns\EnterpriseNotifiableNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Notifications\Slack\SlackAttachment;

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
        return ['mail', 'database', 'broadcast', 'slack'];
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

    public function toSlack($notifiable): SlackMessage
    {

        Log::channel('notification')->info('Slack channel called');
        $order = $this->order;

        $highValueThreshold = 10000;

        $isHighValue = $order->total_amount >= $highValueThreshold;

        $color = $isHighValue ? 'warning' : 'good';

        $emoji = $isHighValue ? '🔥' : '🛒';

        return (new SlackMessage)
            ->to('#orders')
            ->success()
            ->attachment(function (SlackAttachment $attachment) use (
                $order,
                $color,
                $emoji,
                $isHighValue
            ) {
                $attachment
                    ->color($color)
                    ->title(
                        "{$emoji} New Order #{$order->order_number}"
                    )
                    ->content(
                        "Customer: {$order->user?->name}"
                    )
                    ->fields([
                        'Order ID' => $order->id,
                        'Customer' => $order->user?->name ?? 'Customer',
                        'Total Amount' => '₹' . number_format($order->total_amount, 2),
                        'Item Count' => $order->items->count(),
                        'Payment Method' => ucfirst($order->payment_method ?? 'N/A'),
                        'Priority' => $isHighValue ? 'High Value Order' : 'Normal',
                    ])
                    ->action(
                        'View Order',
                        route('admin.orders.show', $order)
                    );
            });
    }
}
