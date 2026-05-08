<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderShipped extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

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
        Log::channel('mail')->info('Notification via() called', [
            'channels' => ['mail', 'database'],
            'notifiable_id' => $notifiable->id,
        ]);

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        Log::channel('mail')->info('Sending OrderShipped mail notification', [
            'order_id' => $this->order->id,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Your order has been shipped.')
            ->line('Tracking Number: '.$this->order->tracking_number)
            ->action('View Order', url('/orders/'.$this->order->id))
            ->line('Thank you for shopping with us!');
    }

    /**
     * Store notification in database.
     */
    public function toArray($notifiable): array
    {
        Log::channel('mail')->info('Saving OrderShipped database notification', [
            'order_id' => $this->order->id,
            'user_id' => $notifiable->id,
        ]);

        return [
            'title' => 'Order Shipped',
            'order_id' => $this->order->id,
            'tracking_number' => $this->order->tracking_number,
            'message' => 'Your order has been shipped!',
            'icon' => 'truck',
        ];
    }
}
