<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Admin\OrderService;
use App\Http\Requests\UpdateOrderStatusRequest;

class AdminOrderController extends Controller
{
    protected $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $orders = $this->service->getAllOrders();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->service->getOrderDetails($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->service->updateStatus($order, $request->status);

        return back()->with('success', 'Order status updated successfully');
    }
}