@extends('layouts.admin')

@section('page-title', 'Orders')

@section('content')
<section class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-base font-semibold text-slate-900">Orders</h2>
        <p class="mt-1 text-sm text-slate-500">Track customer orders and update fulfillment status.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">Order ID</th>
                    <th class="px-5 py-3 text-left font-medium">Customer</th>
                    <th class="px-5 py-3 text-left font-medium">Total</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-left font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-900">
                            {{ $order->order_number ?? ('#' . $order->id) }}
                        </td>
                        <td class="px-5 py-3 text-slate-700">{{ $order->user->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3 text-slate-700">Rs. {{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="{{ $order->status === 'delivered' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-700') }} inline-flex rounded-full px-2.5 py-1 text-xs font-medium">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-500">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-200 px-5 py-4">
        {{ $orders->links() }}
    </div>
</section>
@endsection
