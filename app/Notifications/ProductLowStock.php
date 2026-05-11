<?php

namespace App\Notifications;

use App\Models\Product;
use App\Notifications\Concerns\EnterpriseNotifiableNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ProductLowStock extends EnterpriseNotifiableNotification
{
    /**
     * Create notification instance.
     */
    public function __construct(
        public readonly Product $product,
    ) {
        $this->afterCommit();
    }

    /**
     * Delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Mail notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert')
            ->greeting('Hello Admin,')
            ->line('A product is running low on stock.')
            ->line('Product: '.$this->product->name)
            ->line('Current Stock: '.$this->product->stock)
            ->action(
                'View Product',
                route('admin.products.edit', $this->product->id)
            )
            ->line('Please restock soon.');
    }

    /**
     * Database notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Low Stock Alert',
            'audience' => 'admin',
            'message' => $this->product->name.' stock is low.',
            'product_id' => $this->product->id,
            'stock' => $this->product->stock,
            'icon' => 'warning',
            'action_url' => route('admin.products.edit', $this->product),
            'action_label' => 'Review Product',
            'meta' => [
                'name' => $this->product->name,
                'sku' => $this->product->sku ?? null,
                'threshold' => (int) config('mail.low_stock_threshold', 10),
            ],
        ];
    }
}
