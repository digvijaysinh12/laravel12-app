@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Products</p>
            <p class="text-2xl font-bold">{{ $stats['total_products'] }}</p>
        </div>

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Orders</p>
            <p class="text-2xl font-bold">{{ $stats['total_orders'] }}</p>
        </div>

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Users</p>
            <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
        </div>

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Revenue</p>
            <p class="text-2xl font-bold">
                Rs. {{ number_format($stats['total_revenue'], 2) }}
            </p>
        </div>

    </div>

    <!-- Extra Stats -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded shadow">
            Pending Orders: {{ $stats['pending_orders'] }}
        </div>
        <div class="bg-white p-4 rounded shadow">
            Low Stock Products: {{ $stats['low_stock_products'] }}
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white p-5 rounded shadow">
        <h2 class="font-semibold mb-3">Recent Orders</h2>

        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>User</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                        <td>Rs. {{ $order->total_amount }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No orders</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection