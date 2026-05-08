<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\AdminManualAlert;
use App\Notifications\NewOrderReceived;
use App\Notifications\OrderConfirmation;
use App\Notifications\OrderShipped;
use App\Notifications\ProductLowStock;
use App\Services\NotificationService;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

function makeOrderForNotifications(array $userOverrides = [], array $orderOverrides = []): array
{
    $user = User::factory()->create(array_merge([
        'preferred_locale' => 'en',
    ], $userOverrides));

    $category = Category::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'stock' => 4,
        'price' => 149.99,
    ]);

    $order = Order::factory()->create(array_merge([
        'user_id' => $user->id,
        'order_number' => 'ORD-NTF-1001',
        'status' => 'shipped',
        'tracking_number' => 'TRK-12345',
        'total_amount' => 149.99,
        'payment_method' => 'COD',
        'payment_status' => 'paid',
        'shipping_address' => '123 Example Street',
        'phone' => '9999999999',
    ], $orderOverrides));

    return [$user, $product, $order];
}

it('sends order shipped through mail and database channels', function () {
    Notification::fake();

    [$user, , $order] = makeOrderForNotifications();

    $user->notify(new OrderShipped($order));

    Notification::assertSentTo($user, OrderShipped::class, function (OrderShipped $notification, array $channels) use ($user, $order) {
        expect($channels)->toBe(['mail', 'database']);
        expect($notification->toArray($user))->toMatchArray([
            'title' => 'Order Shipped',
            'order_id' => $order->id,
        ]);

        return true;
    });
});

it('sends guest checkout confirmation as an on-demand notification', function () {
    Notification::fake();

    [, , $order] = makeOrderForNotifications();

    app(NotificationService::class)->sendOnDemand(
        'guest@example.test',
        new OrderConfirmation($order, 'guest@example.test'),
        ['source' => 'guest_checkout'],
    );

    Notification::assertSentOnDemand(OrderConfirmation::class, function (OrderConfirmation $notification, array $channels, AnonymousNotifiable $notifiable) use ($order) {
        expect($channels)->toBe(['mail']);
        expect($notification->order->is($order))->toBeTrue();

        return $notifiable->routes['mail'] === 'guest@example.test';
    });
});

it('sends admin manual alerts as on-demand notifications', function () {
    Notification::fake();

    app(NotificationService::class)->sendOnDemand(
        'ops@example.test',
        new AdminManualAlert('Warehouse alert', 'Please verify shipment holds.'),
        ['source' => 'manual_alert'],
    );

    Notification::assertSentOnDemand(AdminManualAlert::class, function (AdminManualAlert $notification, array $channels, AnonymousNotifiable $notifiable) {
        expect($channels)->toBe(['mail']);
        expect($notification->subjectLine)->toBe('Warehouse alert');

        return $notifiable->routes['mail'] === 'ops@example.test';
    });
});

it('builds the expected admin notification payloads and channels', function () {
    [$admin, $product, $order] = makeOrderForNotifications([
        'role' => 'admin',
    ]);

    $newOrder = new NewOrderReceived($order->fresh('user'));
    $lowStock = new ProductLowStock($product);

    expect($newOrder->via($admin))->toBe(['mail', 'database', 'broadcast', \App\Notifications\Channels\WebhookChannel::class]);
    expect($newOrder->toArray($admin))->toMatchArray([
        'title' => 'New Order Received',
        'order_id' => $order->id,
    ]);

    expect($lowStock->via($admin))->toBe(['mail', 'database']);
    expect($lowStock->toArray($admin))->toMatchArray([
        'title' => 'Low Stock Alert',
        'product_id' => $product->id,
        'stock' => $product->stock,
    ]);
});

it('marks notifications as read through the unified controller routes', function () {
    [$user, , $order] = makeOrderForNotifications();

    $user->notify(new OrderShipped($order));
    $notificationId = $user->fresh()->notifications()->firstOrFail()->id;

    $this->actingAs($user)
        ->post(route('notifications.read', $notificationId), [], [
            'Accept' => 'application/json',
        ])
        ->assertOk()
        ->assertJsonPath('status', 'ok');

    expect($user->fresh()->notifications()->firstOrFail()->read_at)->not->toBeNull();
});
