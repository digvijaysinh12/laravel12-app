@extends('admin.layouts.app')

@section('page-title', 'Reports')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.card title="Revenue"><p class="text-2xl font-semibold text-slate-900">Rs. {{ number_format($summary['total_revenue'] ?? 0, 2) }}</p></x-admin.card>
        <x-admin.card title="Orders"><p class="text-2xl font-semibold text-slate-900">{{ $summary['orders_count'] ?? 0 }}</p></x-admin.card>
        <x-admin.card title="Top Customers"><p class="text-2xl font-semibold text-slate-900">{{ $summary['top_customer_count'] ?? 0 }}</p></x-admin.card>
        <x-admin.card title="Exports"><x-admin.button href="{{ route('admin.reports.export') }}" variant="secondary">CSV Export</x-admin.button></x-admin.card>
    </div>

    <x-admin.card title="Monthly sales" description="Revenue and order volume by month.">
        <x-admin.table :headers="['Month', 'Revenue', 'Average Order', 'Orders']">
            @forelse ($monthlySales as $month => $data)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $month }}</td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($data['revenue']) }}</td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($data['average'], 2) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $data['orders_count'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>

    <x-admin.card title="Top customers" description="Customers ranked by spend.">
        <x-admin.table :headers="['Customer', 'Total Spent', 'Orders']">
            @forelse ($topCustomers as $customer)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">#{{ $customer['user_id'] }}</td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($customer['total_spent']) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $customer['orders'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>

    <x-admin.card title="Category sales" description="Sales grouped by product category.">
        <x-admin.table :headers="['Category', 'Total Revenue']">
            @forelse ($categorySales as $categoryId => $category)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $category['category_name'] }} <span class="text-xs text-slate-400">#{{ $categoryId }}</span></td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($category['total_revenue'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>
</div>
@endsection
