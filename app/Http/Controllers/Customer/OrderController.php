<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Customer\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request): View
    {
        $orders = $this->orderService->paginateForUser($request->user());

        return view('user.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        $order = $this->orderService->getOrderForUser($request->user(), $order);

        return view('user.orders.show', compact('order'));
    }
}
