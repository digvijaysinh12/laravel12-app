@extends('admin.layouts.app')

@section('page-title', 'Products')

@section('content')
<div class="space-y-6">
    <x-admin.card title="Products" description="Search, filter, and manage catalog items.">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid gap-4 md:grid-cols-4">
            <x-admin.input name="search" label="Search" :value="request('search')" placeholder="Product name" />
            <x-admin.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')->all()" :selected="request('category_id')" placeholder="All categories" />
            <x-admin.select name="sort" label="Sort" :options="['newest' => 'Newest', 'price_asc' => 'Price low to high', 'price_desc' => 'Price high to low']" :selected="request('sort', 'newest')" />
            <div class="flex items-end gap-2">
                <x-admin.button type="submit">Filter</x-admin.button>
                <x-admin.button href="{{ route('admin.products.index') }}" variant="secondary">Reset</x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card title="Catalog" description="All products in the store.">
        <div class="mb-4 flex items-center justify-end gap-2">
            <x-admin.button href="{{ route('admin.products.export') }}" variant="secondary">Export</x-admin.button>
            <x-admin.button href="{{ route('admin.products.create') }}">Add Product</x-admin.button>
        </div>

        <x-admin.table :headers="['Product', 'Price', 'Stock', 'Actions']">
            @forelse ($products as $product)
                @php $stock = (int) ($product->stock ?? 0); @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $product->name }}</div>
                        <div class="text-xs text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($product->price, 2) }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :tone="$stock > 10 ? 'success' : ($stock > 0 ? 'warning' : 'danger')">
                            {{ $stock > 0 ? $stock.' in stock' : 'Out of stock' }}
                        </x-admin.badge>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-admin.button href="{{ route('admin.products.edit', $product) }}" variant="secondary" class="px-3 py-1.5 text-xs">Edit</x-admin.button>

                            <div x-data="{ open: false }">
                                <x-admin.button type="button" variant="danger" class="px-3 py-1.5 text-xs" @click="open = true">Delete</x-admin.button>
                                <x-admin.modal
                                    id="deleteModal-{{ $product->id }}"
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition.opacity
                                    title="Delete product"
                                >
                                    <p class="text-sm text-slate-600">Delete <span class="font-medium text-slate-900">{{ $product->name }}</span>? This action cannot be undone.</p>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-5 flex items-center justify-end gap-2">
                                        @csrf
                                        @method('DELETE')
                                        <x-admin.button type="button" variant="secondary" @click="open = false">Cancel</x-admin.button>
                                        <x-admin.button type="submit" variant="danger">Delete</x-admin.button>
                                    </form>
                                </x-admin.modal>
                            </div>
                        </div>
                    </td>
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
