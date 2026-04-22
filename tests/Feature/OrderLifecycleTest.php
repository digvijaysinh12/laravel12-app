<?php

use App\Events\OrderPaid;
use App\Events\OrderShipped;
use App\Events\OrderStatusUpdated;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\OrderService;
use Illuminate\Support\Facades\Event;

function createOrderFixture(array $orderOverrides = [], array $productOverrides = [], int $quantity = 3): array
{
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create(array_merge([
        'category_id' => $category->id,
        'price' => 100,
        'stock' => 7,
    ], $productOverrides));

    $order = Order::factory()->create(array_merge([
        'user_id' => $user->id,
        'status' => 'pending',
        'payment_method' => 'COD',
        'payment_status' => 'pending',
        'shipping_address' => '123 Demo Street',
        'phone' => '9999999999',
        'total_amount' => $quantity * (float) $product->price,
    ], $orderOverrides));

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => $quantity,
        'price' => $product->price,
    ]);

    return [$user, $product, $order];
}

function orderPayload(User $user, Product $product, array $overrides = [], int $quantity = 3): array
{
    return array_merge([
        'user_id' => $user->id,
        'status' => 'pending',
        'payment_method' => 'COD',
        'payment_status' => 'pending',
        'shipping_address' => '123 Demo Street',
        'phone' => '9999999999',
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ],
        ],
    ], $overrides);
}

it('dispatches order paid from the real admin update flow', function () {
    Event::fake();

    [$user, $product, $order] = createOrderFixture();

    app(OrderService::class)->updateOrder($order, orderPayload($user, $product, [
        'payment_status' => 'paid',
    ]));

    Event::assertDispatched(OrderPaid::class, fn (OrderPaid $event) => $event->order->is($order));
    Event::assertNotDispatched(OrderShipped::class);
    Event::assertNotDispatched(OrderStatusUpdated::class);
});

it('dispatches shipped and status-updated events from real status transitions', function () {
    Event::fake();

    [, , $order] = createOrderFixture([
        'status' => 'confirmed',
    ]);

    app(OrderService::class)->updateStatus($order, 'shipped');

    Event::assertDispatched(OrderShipped::class, fn (OrderShipped $event) => $event->order->is($order));
    Event::assertDispatched(OrderStatusUpdated::class, fn (OrderStatusUpdated $event) => $event->order->is($order) && $event->order->status === 'shipped');
});

it('does not decrement stock again when order paid is dispatched', function () {
    config(['broadcasting.default' => 'log']);

    [, $product, $order] = createOrderFixture();

    event(new OrderPaid($order->fresh('items.product', 'user')));

    expect($product->fresh()->stock)->toBe(7);
});

it('promotes overdue pending orders through the lifecycle service', function () {
    Event::fake();

    [, , $order] = createOrderFixture([
        'created_at' => now()->subHours(25),
        'updated_at' => now()->subHours(25),
    ]);

    $updatedCount = app(OrderService::class)->promotePendingOrdersToConfirmed();

    expect($updatedCount)->toBe(1);
    expect($order->fresh()->status)->toBe('confirmed');

    Event::assertDispatched(OrderStatusUpdated::class, fn (OrderStatusUpdated $event) => $event->order->is($order) && $event->order->status === 'confirmed');
});
