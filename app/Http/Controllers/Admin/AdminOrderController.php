<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function __construct(protected OrderService $service)
    {
    }

    public function index(): View
    {
        $orders = $this->service->getAllOrders();

        return view('admin.orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('admin.orders.create', $this->formData());
    }

    public function store(ManageOrderRequest $request): RedirectResponse
    {
        $order = $this->service->createOrder($request->validated());

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order): View
    {
        $order = $this->service->getOrderDetails((int) $order->id);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $order->load('items.product', 'user');

        return view('admin.orders.edit', array_merge(
            $this->formData(),
            ['order' => $order]
        ));
    }

    public function update(ManageOrderRequest $request, Order $order): RedirectResponse
    {
        $updatedOrder = $this->service->updateOrder($order, $request->validated());

        return redirect()
            ->route('admin.orders.show', $updatedOrder)
            ->with('success', 'Order updated successfully.');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->service->updateStatus($order, $request->validated('status'));

        return back()->with('success', 'Order status updated successfully');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->service->deleteOrder($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'customers' => User::query()
                ->where('role', 'user')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'products' => Product::query()
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'stock']),
            'statuses' => [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled',
            ],
            'paymentStatuses' => [
                'pending' => 'Pending',
                'paid' => 'Paid',
                'failed' => 'Failed',
                'refunded' => 'Refunded',
            ],
            'paymentMethods' => [
                'COD' => 'Cash on Delivery',
                'card' => 'Card',
                'upi' => 'UPI',
                'bank_transfer' => 'Bank Transfer',
            ],
        ];
    }
}
