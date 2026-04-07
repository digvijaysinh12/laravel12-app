@extends('layouts.admin')

@section('page-title', 'Sales Report')

@section('content')

<!-- 📊 Monthly Sales -->
<section class="rounded-xl border border-slate-200 bg-white shadow-sm mb-8">

    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-base font-semibold text-slate-900">Monthly Sales</h2>
        <p class="mt-1 text-sm text-slate-500">
            Overview of revenue, average order value, and total orders per month.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">Month</th>
                    <th class="px-5 py-3 text-left font-medium">Revenue</th>
                    <th class="px-5 py-3 text-left font-medium">Average Order</th>
                    <th class="px-5 py-3 text-left font-medium">Orders</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse ($monthlySales as $month => $data)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-900">
                            {{ $month }}
                        </td>

                        <td class="px-5 py-3 text-emerald-600 font-semibold">
                            ₹{{ number_format($data['total_revenue']) }}
                        </td>

                        <td class="px-5 py-3 text-slate-700">
                            ₹{{ number_format($data['average_order'], 2) }}
                        </td>

                        <td class="px-5 py-3 text-slate-700">
                            {{ $data['orders_count'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-500">
                            No sales data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>


<!-- 👑 Top Customers -->
<section class="rounded-xl border border-slate-200 bg-white shadow-sm">

    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-base font-semibold text-slate-900">Top Customers</h2>
        <p class="mt-1 text-sm text-slate-500">
            Customers ranked by total spending and order count.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">User ID</th>
                    <th class="px-5 py-3 text-left font-medium">Total Spent</th>
                    <th class="px-5 py-3 text-left font-medium">Orders</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse ($topCustomers as $customer)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-900">
                            #{{ $customer['user_id'] }}
                        </td>

                        <td class="px-5 py-3 text-sky-600 font-semibold">
                            ₹{{ number_format($customer['total_spent']) }}
                        </td>

                        <td class="px-5 py-3 text-slate-700">
                            {{ $customer['orders'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-slate-500">
                            No customer data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>

@endsection