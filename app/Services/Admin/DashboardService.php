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
            $productStats = Product::query()
                ->selectRaw('COUNT(*) as total_products')
                ->selectRaw('SUM(CASE WHEN stock < 10 THEN 1 ELSE 0 END) as low_stock_products')
                ->first();

            $orderStats = Order::query()
                ->selectRaw('COUNT(*) as total_orders')
                ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
                ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders')
                ->first();

            return [
                'total_products' => (int) ($productStats->total_products ?? 0),
                'total_orders' => (int) ($orderStats->total_orders ?? 0),
                'total_users' => User::count(),
                'total_revenue' => (float) ($orderStats->total_revenue ?? 0),
                'pending_orders' => (int) ($orderStats->pending_orders ?? 0),
                'low_stock_products' => (int) ($productStats->low_stock_products ?? 0),
            ];
        });

        $recentOrders = $this->adminCache()->remember('admin.recent.orders', 600, function () {
            return Order::query()
                ->select([
                    'id',
                    'user_id',
                    'order_number',
                    'total_amount',
                    'status',
                    'created_at',
                ])
                ->with('user:id,name')
                ->latest('id')
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
