@extends('admin.layouts.app')

@section('page-title', 'Inventory')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2">
        <x-admin.card title="Low Stock" description="Products at or below the restock threshold.">
            <p class="text-2xl font-semibold text-slate-900">{{ $lowStockCount }}</p>
        </x-admin.card>

        <x-admin.card title="Inventory Notes" description="Keep this page focused on what needs attention first.">
            <p class="text-sm text-slate-600">Use this list to review stock levels, replenish products, and spot items that need action.</p>
        </x-admin.card>
    </div>

    <x-admin.card title="Stock list" description="Product stock sorted from low to high.">
        <x-admin.table :headers="['Product', 'Category', 'Stock', 'Price']">
            @forelse ($products as $product)
                @php $stock = (int) ($product->stock ?? 0); @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $product->category->name ?? 'Uncategorized' }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :tone="$stock <= 10 ? 'warning' : 'success'">{{ $stock }}</x-admin.badge>
                    </td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($product->price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
