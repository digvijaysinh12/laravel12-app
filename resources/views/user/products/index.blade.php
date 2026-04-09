@extends('user.layouts.app')

@section('title', $page_title)

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Catalog</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ $page_title }}</h1>
            <p class="text-sm text-slate-600">Showing {{ $total_products }} products</p>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="GET" action="{{ $listing_route ?? route('user.products.index') }}" class="grid gap-3 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Search products">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                <select name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                    <option value="">All categories</option>
                    @foreach(($categories ?? []) as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Sort</label>
                <select name="sort" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Price Low-High</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price High-Low</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-sky-700">Filter</button>
                <a href="{{ $listing_route ?? route('user.products.index') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($products as $product)
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="h-44 overflow-hidden rounded-xl bg-slate-100">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @endif
                    </div>

                    <div class="mt-4 space-y-2">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $product->name }}</h2>
                        <p class="text-sm text-slate-600">{{ $product->description ?: 'No description available.' }}</p>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-base font-semibold text-slate-900">INR {{ number_format($product->price, 2) }}</span>
                            <a href="{{ route('user.products.show', $product) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">View</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-sm text-slate-600">
                    No products found.
                </div>
            @endforelse
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
            {{ $products->links() }}
        </div>
    </section>
</div>
@endsection
