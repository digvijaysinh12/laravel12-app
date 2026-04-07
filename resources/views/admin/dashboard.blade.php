@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Products</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalProducts }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Orders</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalOrders }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Users</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalUsers }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Revenue</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">Rs. {{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">Recent Orders</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Order No</th>
                        <th class="px-5 py-3 text-left font-medium">Customer</th>
                        <th class="px-5 py-3 text-left font-medium">Total</th>
                        <th class="px-5 py-3 text-left font-medium">Status</th>
                        <th class="px-5 py-3 text-left font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-900">{{ $order->order_number }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $order->user->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-slate-700">Rs. {{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="{{ $order->status === 'delivered' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-700') }} inline-flex rounded-full px-2.5 py-1 text-xs font-medium">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $order->created_at?->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">No recent orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.__adminRealtimeBound) {
        return;
    }

    window.__adminRealtimeBound = true;

    const pusherKey = @json(config('broadcasting.connections.pusher.key'));
    const pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const getContainer = () => {
        let container = document.querySelector('[data-admin-realtime]');

        if (!container) {
            container = document.createElement('div');
            container.setAttribute('data-admin-realtime', 'true');
            container.className = 'fixed right-4 top-20 z-50 flex max-w-sm flex-col gap-2';
            document.body.appendChild(container);
        }

        return container;
    };

    const showNotice = (message) => {
        const item = document.createElement('div');
        item.className = 'rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm';
        item.textContent = message;
        getContainer().appendChild(item);
        setTimeout(() => item.remove(), 5000);
    };

    const handleOrderPlaced = (event) => {
        const orderNo = event.order_number || ('#' + (event.order_id || ''));
        const items = event.item_count ?? event.items ?? 0;
        const customer = event.customer_name || 'Customer';
        const total = Number(event.total || 0).toLocaleString('en-IN', {
            style: 'currency',
            currency: 'INR',
        });

        showNotice(`${customer} placed ${orderNo} (${items} items, ${total}).`);
    };

    if (window.Pusher && pusherKey && csrfToken) {
        const pusher = new window.Pusher(pusherKey, {
            cluster: pusherCluster,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            },
        });

        const channel = pusher.subscribe('private-admin.orders');
        channel.bind('order.placed', handleOrderPlaced);
        channel.bind('OrderPlaced', handleOrderPlaced);
        channel.bind('App\\Events\\OrderPlaced', handleOrderPlaced);
        return;
    }

    if (window.Echo) {
        window.Echo.private('admin.orders')
            .listen('.order.placed', handleOrderPlaced)
            .listen('OrderPlaced', handleOrderPlaced);
    }
});
</script>
@endpush
