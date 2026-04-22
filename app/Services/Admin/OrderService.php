<?php

namespace App\Services\Admin;

use App\Events\OrderDelivered;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Events\OrderShipped;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    private const NOTIFIABLE_STATUSES = ['confirmed', 'shipped', 'delivered'];

    public function getAllOrders(): LengthAwarePaginator
    {
        return Order::with('user')
            ->latest()
            ->paginate(10);
    }

    public function getOrderDetails(int $id): Order
    {
        return Order::with('items.product', 'user')
            ->findOrFail($id);
    }

    public function createOrder(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                'user_id' => (int) $data['user_id'],
                'order_number' => $this->generateOrderNumber(),
                'total_amount' => 0,
                'status' => $data['status'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
                'shipping_address' => $data['shipping_address'],
                'phone' => $data['phone'],
            ]);

            $this->syncOrderItems($order, $data['items']);

            return $order->fresh(['items.product', 'user']);
        });

        event(new OrderPlaced($order));
        $this->dispatchLifecycleEvents($order);
        $this->clearOrderCaches([$order->user_id]);

        return $order;
    }

    public function updateOrder(Order $order, array $data): Order
    {
        $originalUserId = (int) $order->user_id;
        $originalStatus = $order->status;
        $originalPaymentStatus = $order->payment_status;

        $updatedOrder = DB::transaction(function () use ($order, $data, $originalStatus) {
            $order->loadMissing('items.product');
            $this->restoreStockFromItems($order, $originalStatus);
            $order->items()->delete();

            $order->update([
                'user_id' => (int) $data['user_id'],
                'status' => $data['status'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
                'shipping_address' => $data['shipping_address'],
                'phone' => $data['phone'],
            ]);

            $this->syncOrderItems($order, $data['items']);

            return $order->fresh(['items.product', 'user']);
        });

        $this->dispatchLifecycleEvents($updatedOrder, $originalStatus, $originalPaymentStatus);

        $this->clearOrderCaches([$originalUserId, $updatedOrder->user_id]);

        return $updatedOrder;
    }

    public function deleteOrder(Order $order): void
    {
        $userId = (int) $order->user_id;

        DB::transaction(function () use ($order) {
            $order->loadMissing('items.product');
            $this->restoreStockFromItems($order, $order->status);
            $order->delete();
        });

        $this->clearOrderCaches([$userId]);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $previousStatus = $order->status;

        DB::transaction(function () use ($order, $status, $previousStatus) {
            $order->loadMissing('items.product');

            if ($previousStatus !== 'cancelled' && $status === 'cancelled') {
                $this->restoreStockFromItems($order, $previousStatus);
            }

            if ($previousStatus === 'cancelled' && $status !== 'cancelled') {
                $this->applyStockForItems($order->items, $status);
            }

            $order->update([
                'status' => $status,
            ]);
        });

        $order->loadMissing('user');

        $this->dispatchLifecycleEvents($order, $previousStatus, $order->payment_status);

        $this->clearOrderCaches([$order->user_id]);

        return $order;
    }

    public function promotePendingOrdersToConfirmed(): int
    {
        $orders = Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($orders as $order) {
            $this->updateStatus($order, 'confirmed');
        }

        return $orders->count();
    }

    private function syncOrderItems(Order $order, array $items): void
    {
        $totalAmount = 0;
        $normalizedItems = collect($items)
            ->map(function (array $item) {
                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ];
            })
            ->filter(fn (array $item) => $item['product_id'] > 0 && $item['quantity'] > 0)
            ->values();

        $products = Product::query()
            ->whereIn('id', $normalizedItems->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        foreach ($normalizedItems as $item) {
            /** @var Product|null $product */
            $product = $products->get($item['product_id']);

            if (! $product) {
                continue;
            }

            if ($order->status !== 'cancelled') {
                $this->guardStock($product, $item['quantity']);
                $product->decrement('stock', $item['quantity']);
            }

            $linePrice = (float) $product->price;
            $lineTotal = $linePrice * $item['quantity'];
            $totalAmount += $lineTotal;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $linePrice,
            ]);
        }

        $order->update([
            'total_amount' => round($totalAmount, 2),
        ]);
    }

    private function restoreStockFromItems(Order $order, string $status): void
    {
        if ($status === 'cancelled') {
            return;
        }

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }

    private function applyStockForItems(Collection $items, string $status): void
    {
        if ($status === 'cancelled') {
            return;
        }

        foreach ($items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->guardStock($item->product, (int) $item->quantity);
            $item->product->decrement('stock', $item->quantity);
        }
    }

    private function guardStock(Product $product, int $quantity): void
    {
        if ((int) $product->stock < $quantity) {
            throw new \RuntimeException("Not enough stock for {$product->name}.");
        }
    }

    private function dispatchLifecycleEvents(
        Order $order,
        ?string $previousStatus = null,
        ?string $previousPaymentStatus = null
    ): void {
        $order->loadMissing('items.product', 'user');

        if ($previousPaymentStatus !== 'paid' && $order->payment_status === 'paid') {
            Log::info('Dispatching order paid event', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);

            event(new OrderPaid($order));
        }

        if ($previousStatus === $order->status || ! in_array($order->status, self::NOTIFIABLE_STATUSES, true)) {
            return;
        }

        if ($order->status === 'shipped') {
            Log::info('Dispatching order shipped event', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);

            event(new OrderShipped($order));
        }

        if ($order->status === 'delivered') {
            Log::info('Dispatching order delivered event', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);

            event(new OrderDelivered($order));
        }

        Log::info('Broadcasting order status update event', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => $order->status,
        ]);

        event(new OrderStatusUpdated($order));
    }

    private function clearOrderCaches(array $userIds): void
    {
        $this->clearAdminAnalyticsCache();

        foreach (array_unique(array_map('intval', $userIds)) as $userId) {
            if ($userId > 0) {
                Cache::forget("customer.order.analytics.{$userId}");
            }
        }
    }

    private function clearAdminAnalyticsCache(): void
    {
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['admin'])->flush();

            return;
        }

        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.recent.orders');
        Cache::forget('admin.sales.analytics');
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ADM-ORD-'.now()->format('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
