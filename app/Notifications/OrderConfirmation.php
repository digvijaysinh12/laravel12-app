<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\EnterpriseNotifiableNotification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderConfirmation extends EnterpriseNotifiableNotification
{
    public function __construct(
        public readonly Order $order,
        public readonly ?string $guestEmail = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Order Confirmation #:number', ['number' => $this->order->order_number]))
            ->greeting(__('Hello!'))
            ->line(__('Your order has been placed successfully.'))
            ->line(__('Order Number: :number', ['number' => $this->order->order_number]))
            ->line(__('Order Total: :amount', ['amount' => number_format((float) $this->order->total_amount, 2)]))
            ->action(__('View Order'), route('user.orders.show', $this->order))
            ->line(__('Thank you for shopping with us.'));
    }
}
