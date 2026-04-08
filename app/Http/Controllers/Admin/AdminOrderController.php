<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\Admin\OrderService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

    public function show(int $id): View
    {
        $order = $this->service->getOrderDetails($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->service->updateStatus($order, $request->status);

        return back()->with('success', 'Order status updated successfully');
    }
}
