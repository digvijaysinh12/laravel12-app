<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 🔹 Basic Stats
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $totalUsers    = User::count();

        $totalRevenue  = Order::sum('total_amount');

        // 🔹 Recent Orders (with user relation)
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalUsers',
            'totalRevenue',
            'recentOrders'
        ));
    }
}