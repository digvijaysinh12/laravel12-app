@extends('layouts.app')

@section('title', $page_title)

@section('content')
<div class="space-y-8">
    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Browse catalog</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $page_title }}</h1>
                <p class="mt-2 text-sm text-slate-500">Total products: {{ $total_products }}</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('user.products.index') }}" class="flex w-full gap-2 sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white sm:w-72"
                        placeholder="Search products...">
                    <x-button type="submit" variant="secondary">Search</x-button>
                </form>

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <x-button href="{{ route('admin.products.export') }}" variant="secondary">Download CSV</x-button>
                    <x-button href="{{ route('admin.products.create') }}">Add Product</x-button>
                @endif
            </div>
        </div>
    </section>

    <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-sm text-slate-500">
                No products available.
            </div>
        @endforelse
    </section>

    <div class="flex flex-col gap-3 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <span>Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</span>
        <div class="rounded-2xl border border-slate-200 bg-white px-3 py-2">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
