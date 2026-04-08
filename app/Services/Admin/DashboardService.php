<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getDashboardData(): array
    {
        $stats = $this->adminCache()->remember('admin.dashboard.stats', 600, function () {
            return [
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_users' => User::count(),
                'total_revenue' => (float) Order::sum('total_amount'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'low_stock_products' => Product::where('stock', '<', 10)->count(),
            ];
        });

        $recentOrders = $this->adminCache()->remember('admin.recent.orders', 600, function () {
            return Order::with('user')
                ->latest()
                ->take(5)
                ->get();
        });

        return [
            'totalProducts' => $stats['total_products'],
            'totalOrders' => $stats['total_orders'],
            'totalUsers' => $stats['total_users'],
            'totalRevenue' => $stats['total_revenue'],
            'recentOrders' => $recentOrders,
            'stats' => $stats,
        ];
    }

    private function adminCache()
    {
        return $this->supportsTags()
            ? Cache::tags(['admin'])
            : Cache::store();
    }

    private function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
