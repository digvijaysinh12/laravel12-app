<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Slack\SlackMessage;
use App\Notifications\Concerns\EnterpriseNotifiableNotification;

use App\Notifications\NewOrderReceived;
use App\Notifications\ProductLowStock;

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
        return ['database', 'slack'];
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
            ->line('Product: ' . $this->product->name)
            ->line('Current Stock: ' . $this->product->stock)
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
            'message' => $this->product->name . ' stock is low.',
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

    /**
     * Slack notification.
     */

public function toSlack($notifiable): SlackMessage
{
    // Step 1: confirm this method is even being called
    Log::info('ProductLowStock::toSlack called', [
        'product_id' => $this->product->id,
        'stock'      => $this->product->stock,
    ]);

    $critical = $this->product->stock < 5;

    Log::info('ProductLowStock: criticality determined', [
        'critical' => $critical,
    ]);

    $message = "• {$this->product->name} ({$this->product->stock} left)";

    $slackMessage = (new SlackMessage)
        ->to('#alerts')
        ->warning()
        ->content('⚠️ Low Stock Alert')
        ->attachment(function ($attachment) use ($message, $critical) {
            $content = $critical
                ? "<!subteam^WAREHOUSE_ID>\n\n{$message}"
                : $message;

            $attachment
                ->title('Inventory Warning')
                ->content($content)
                ->color($critical ? 'danger' : 'warning');
        });

    // Step 2: confirm the message object was built without errors
    Log::info('ProductLowStock: SlackMessage built successfully');

    return $slackMessage;
}
}