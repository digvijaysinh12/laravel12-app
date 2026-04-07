@extends('layouts.app')

@section('title', $page_title)

@section('content')
<div class="space-y-6">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $page_title }}</h1>
                <p class="mt-1 text-sm text-slate-600">Showing {{ $total_products }} products</p>
            </div>

            <form method="GET" action="{{ route('user.products.index') }}" class="flex w-full gap-2 md:w-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500 md:w-72"
                    placeholder="Search products"
                >
                <x-button type="submit" variant="secondary">Search</x-button>
            </form>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-sm text-slate-600">
                No products available.
            </div>
        @endforelse
    </section>

    <div class="flex flex-col gap-3 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
        <span>Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</span>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
