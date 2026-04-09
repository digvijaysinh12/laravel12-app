@extends('user.layouts.app')

@section('title', 'Orders')

@section('content')
<section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 px-5 py-4">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Order history</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Your Orders</h1>
        <p class="mt-1 text-sm text-slate-600">Review your completed and pending orders.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">Order ID</th>
                    <th class="px-5 py-3 text-left font-medium">Date</th>
                    <th class="px-5 py-3 text-left font-medium">Items</th>
                    <th class="px-5 py-3 text-left font-medium">Total</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-left font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-slate-700">{{ optional($order->created_at)->format('d M, Y') }}</td>
                        <td class="px-5 py-3 text-slate-700">{{ $order->items_count }}</td>
                        <td class="px-5 py-3 text-slate-700">INR {{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('user.orders.show', $order->id) }}" class="inline-flex rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-sky-700">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                            No orders found.
                        </td>
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
