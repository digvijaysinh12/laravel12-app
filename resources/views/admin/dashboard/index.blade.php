@extends('admin.layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.card title="Revenue">
            <p class="text-2xl font-semibold text-slate-900">Rs. {{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
            <p class="mt-1 text-sm text-slate-500">Total revenue captured</p>
        </x-admin.card>

        <x-admin.card title="Orders">
            <p class="text-2xl font-semibold text-slate-900">{{ $stats['total_orders'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-slate-500">All time orders</p>
        </x-admin.card>

        <x-admin.card title="Customers">
            <p class="text-2xl font-semibold text-slate-900">{{ $stats['total_users'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-slate-500">Registered accounts</p>
        </x-admin.card>

        <x-admin.card title="Low Stock">
            <p class="text-2xl font-semibold text-slate-900">{{ $stats['low_stock_products'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-slate-500">Products to restock</p>
        </x-admin.card>
    </div>

    <x-admin.card title="Recent Orders" description="Latest purchase activity across the store.">
        <x-admin.table :headers="['Order', 'Customer', 'Total', 'Status']">
            @forelse ($recentOrders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $order->order_number }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">Rs. {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :tone="$order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'neutral')">
                            {{ ucfirst($order->status) }}
                        </x-admin.badge>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>
</div>
@endsection
