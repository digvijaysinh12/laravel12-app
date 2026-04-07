@extends('layouts.admin')

@section('page-title', 'Products')

@section('content')
<div class="space-y-5">
    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex w-full max-w-lg gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search products..."
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-500"
                >
                <button type="submit" class="rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">
                    Search
                </button>
            </form>

            <div class="flex items-center gap-2">
                <x-button href="{{ route('admin.products.export') }}" variant="secondary">Export</x-button>
                <x-button href="{{ route('admin.products.create') }}">Add Product</x-button>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Product Name</th>
                        <th class="px-5 py-3 text-left font-medium">Price</th>
                        <th class="px-5 py-3 text-left font-medium">Stock</th>
                        <th class="px-5 py-3 text-left font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($products as $product)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-900">{{ $product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</div>
                            </td>
                            <td class="px-5 py-3 text-slate-700">Rs. {{ number_format($product->price, 2) }}</td>
                            <td class="px-5 py-3">
                                @php $stock = $product->stock ?? 0; @endphp
                                <span class="{{ $stock > 10 ? 'bg-emerald-100 text-emerald-700' : ($stock > 0 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }} inline-flex rounded-full px-2.5 py-1 text-xs font-medium">
                                    {{ $stock > 0 ? $stock . ' in stock' : 'Out of stock' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $products->links() }}
        </div>
    </section>
</div>
@endsection
