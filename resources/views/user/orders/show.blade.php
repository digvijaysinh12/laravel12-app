@extends('user.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="space-y-6" data-order-id="{{ $order->id }}">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Order details</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $order->order_number }}</h1>
                <p class="mt-2 text-sm text-slate-600">Placed on {{ optional($order->created_at)->format('d M, Y h:i A') }}</p>
            </div>
            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-700">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Total</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">INR {{ number_format($order->total_amount, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Payment</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->payment_method ?? 'N/A' }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Payment Status</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ ucfirst($order->payment_status ?? 'N/A') }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Items</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->items->count() }}</p>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Items</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Product</th>
                            <th class="px-4 py-3 text-left font-medium">Qty</th>
                            <th class="px-4 py-3 text-left font-medium">Price</th>
                            <th class="px-4 py-3 text-left font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($order->items as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $item->product->name ?? 'Deleted product' }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->product->category->name ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-slate-700">INR {{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">INR {{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-500">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Customer</h2>
                <div class="mt-4 space-y-2 text-sm text-slate-700">
                    <p><span class="font-medium text-slate-900">Name:</span> {{ $order->user->name ?? 'N/A' }}</p>
                    <p><span class="font-medium text-slate-900">Email:</span> {{ $order->user->email ?? 'N/A' }}</p>
                    <p><span class="font-medium text-slate-900">Phone:</span> {{ $order->phone ?? 'N/A' }}</p>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Shipping Address</h2>
                <p class="mt-3 text-sm leading-6 text-slate-700">{{ $order->shipping_address ?? 'N/A' }}</p>
            </section>

            <a href="{{ route('user.orders.index') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Back to Orders
            </a>
        </aside>
    </div>
</div>
@endsection
