@extends('admin.layouts.app')

@section('page-title', 'Reports')

@section('content')
@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="space-y-6">

    <!-- 🔹 Summary Cards -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.card title="Revenue">
            <p class="text-2xl font-semibold text-slate-900">
                ₹ {{ number_format($summary['total_revenue'] ?? 0, 2) }}
            </p>
        </x-admin.card>

        <x-admin.card title="Orders">
            <p class="text-2xl font-semibold text-slate-900">
                {{ $summary['orders_count'] ?? 0 }}
            </p>
        </x-admin.card>

        <x-admin.card title="Top Customers">
            <p class="text-2xl font-semibold text-slate-900">
                {{ $summary['top_customer_count'] ?? 0 }}
            </p>
        </x-admin.card>

        <x-admin.card title="Generate Reports">
            <div class="flex gap-2">
                <button onclick="exportReport('csv')" class="btn">CSV</button>
                <button onclick="exportReport('json')" class="btn">JSON</button>
                <button onclick="exportReport('pdf')" class="btn">PDF</button>
            </div>
        </x-admin.card>
    </div>

    <!-- 🔹 Monthly Sales -->
    <x-admin.card title="Monthly Sales" description="Revenue and order volume by month.">
        <x-admin.table :headers="['Month', 'Revenue', 'Average Order', 'Orders']">
            @forelse ($monthlySales as $month => $data)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $month }}</td>
                    <td class="px-4 py-3 text-slate-600">₹ {{ number_format($data['revenue']) }}</td>
                    <td class="px-4 py-3 text-slate-600">₹ {{ number_format($data['average'], 2) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $data['orders_count'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">
                        No data found.
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>

    <!-- 🔹 Top Customers -->
    <x-admin.card title="Top Customers" description="Customers ranked by spend.">
        <x-admin.table :headers="['Customer ID', 'Total Spent', 'Orders']">
            @forelse ($topCustomers as $customer)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">
                        #{{ $customer['user_id'] }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        ₹ {{ number_format($customer['total_spent']) }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $customer['orders'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">
                        No data found.
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>

    <!-- 🔹 Category Sales -->
    <x-admin.card title="Category Sales" description="Sales grouped by product category.">
        <x-admin.table :headers="['Category', 'Total Revenue']">
            @forelse ($categorySales as $category)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">
                        {{ $category['category_name'] }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        ₹ {{ number_format($category['total_revenue'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-10 text-center text-sm text-slate-500">
                        No data found.
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>

    <!-- 🔹 Generated Reports -->
    <x-admin.card title="Generated Reports" description="Download previously generated reports.">
        <x-admin.table :headers="['File Name', 'Generated At', 'Action']">

            @forelse ($files as $file)
                <tr class="hover:bg-slate-50">

                    <td class="px-4 py-3 font-medium text-slate-900">
                        {{ basename($file) }}
                    </td>

                    <td class="px-4 py-3 text-slate-600">
                        {{ date('d M Y H:i', Storage::disk('reports')->lastModified($file)) }}
                    </td>

                    <td class="px-4 py-3">
                        <x-admin.button
                            href="{{ route('admin.reports.download', $file) }}"
                            variant="primary"
                        >
                            Download
                        </x-admin.button>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">
                        No reports found. Generate one to get started.
                    </td>
                </tr>
            @endforelse

        </x-admin.table>
    </x-admin.card>

</div>
@push('scripts')
<script>
function showToast(message, tone = 'default') {

    let classes = {
        success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        danger: 'border-rose-200 bg-rose-50 text-rose-800',
        warning: 'border-amber-200 bg-amber-50 text-amber-800',
        default: 'border-slate-200 bg-white text-slate-700'
    };

    let toast = `
        <div class="rounded-xl border px-4 py-3 text-sm shadow-sm ${classes[tone]}">
            ${message}
        </div>
    `;

    $('#toast-container').append(toast);

    setTimeout(() => {
        $('#toast-container div').first().fadeOut(300, function () {
            $(this).remove();
        });
    }, 3000);
}

function exportReport(format) {

    showToast('Generating report...', 'warning');

    $.ajax({
        url: '/admin/reports/export',
        type: 'GET',
        data: { format: format },

        success: function (response) {
            if (response.success) {
                window.location.href = response.download_url;
                showToast(response.message, response.tone);
            }
        },

        error: function () {
            showToast('Failed to generate report', 'danger');
        }
    });
}
</script>
@endpush
@endsection