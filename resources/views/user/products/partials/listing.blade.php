@php
    $currentSort = request('sort', 'newest');
    $selectedCategory = (string) request('category_id', $selected_category ?? '');
    $showInStockOnly = request()->boolean('in_stock');
    $visibleProducts = $showInStockOnly
        ? $products->getCollection()->filter(fn ($product) => (int) ($product->stock ?? 0) > 0)
        : $products->getCollection();
@endphp

<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Catalog</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $page_title }}</h1>
                <p class="mt-2 text-sm text-slate-600">Showing {{ $total_products }} products</p>
                @isset($load_time_ms)
                    <p class="mt-1 text-xs text-slate-500">Loaded in {{ $load_time_ms }} ms</p>
                @endisset
            </div>

            <form method="GET" action="{{ $listing_route ?? route('user.products.index') }}" class="flex items-center gap-3">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="category_id" value="{{ $selectedCategory }}">
                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                @if ($showInStockOnly)
                    <input type="hidden" name="in_stock" value="1">
                @endif
                <label for="sort" class="text-sm font-medium text-slate-700">Sort</label>
                <select id="sort" name="sort" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400">
                    <option value="newest" @selected($currentSort === 'newest')>Newest</option>
                    <option value="price_asc" @selected($currentSort === 'price_asc')>Price Low-High</option>
                    <option value="price_desc" @selected($currentSort === 'price_desc')>Price High-Low</option>
                    <option value="name_asc" @selected($currentSort === 'name_asc')>Name A-Z</option>
                </select>
            </form>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-24">
            <h2 class="text-base font-semibold text-slate-900">Filters</h2>

            <form method="GET" action="{{ $listing_route ?? route('user.products.index') }}" class="mt-4 space-y-5">
                <div>
                    <label for="search" class="mb-1 block text-sm font-medium text-slate-700">Search</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Search products" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400">
                </div>

                <div>
                    <label for="category_id" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                    <select id="category_id" name="category_id" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400">
                        <option value="">All categories</option>
                        @foreach (($categories ?? []) as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="min_price" class="mb-1 block text-sm font-medium text-slate-700">Min Price</label>
                        <input id="min_price" type="number" min="0" name="min_price" value="{{ request('min_price') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400">
                    </div>
                    <div>
                        <label for="max_price" class="mb-1 block text-sm font-medium text-slate-700">Max Price</label>
                        <input id="max_price" type="number" min="0" name="max_price" value="{{ request('max_price') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400">
                    </div>
                </div>

                <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700">
                    <input type="checkbox" name="in_stock" value="1" @checked($showInStockOnly) class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    In stock only
                </label>

                <input type="hidden" name="sort" value="{{ $currentSort }}">

                <div class="flex flex-col gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                        Apply Filters
                    </button>
                    <a href="{{ route('user.products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </aside>

        <section class="space-y-5">
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @forelse ($visibleProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-sm text-slate-600">
                        No products match your filters.
                    </div>
                @endforelse
            </div>

            <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <span>Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</span>
                <div>{{ $products->links() }}</div>
            </div>
        </section>
    </div>
</div>
