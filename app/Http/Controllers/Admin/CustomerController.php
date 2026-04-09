<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer): View
    {
        $customer->loadCount('orders');

        $orders = Order::query()
            ->where('user_id', $customer->id)
            ->latest()
            ->with('items.product')
            ->paginate(10);

        return view('admin.customers.show', compact('customer', 'orders'));
    }
}
