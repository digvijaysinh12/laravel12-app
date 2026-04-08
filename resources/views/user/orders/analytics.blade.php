@extends('layouts.app')

@section('title', 'Order Analytics')

@section('content')
<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Total Orders</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalOrders }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Total Spent</p>
            <p class="mt-3 text-3xl font-semibold text-sky-600">INR {{ number_format($totalSpent, 2) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Average Order Value</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-600">INR {{ number_format($averageOrder, 2) }}</p>
        </article>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Top Products</h2>
                <p class="mt-1 text-sm text-slate-500">Top 3 products by ordered quantity.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">Rank</th>
                            <th class="px-5 py-3 text-left font-medium">Product Name</th>
                            <th class="px-5 py-3 text-left font-medium">Quantity Ordered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($topProducts as $product)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-medium text-slate-900">{{ $loop->iteration }}</td>
                                <td class="px-5 py-3 text-slate-700">{{ $product['product_name'] }}</td>
                                <td class="px-5 py-3 text-slate-700">{{ $product['quantity'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-slate-500">No product order history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Orders by Status</h2>
                <p class="mt-1 text-sm text-slate-500">Distribution of your orders across statuses.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">Status</th>
                            <th class="px-5 py-3 text-left font-medium">Count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($ordersByStatus as $status => $count)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 text-slate-700">{{ $status ? ucfirst($status) : 'N/A' }}</td>
                                <td class="px-5 py-3 font-medium text-slate-900">{{ $count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-8 text-center text-slate-500">No orders found for status analysis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
