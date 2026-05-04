@extends('user.layouts.app')

@section('title', __('products.catalog'))

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">
                {{ __('products.catalog') }}
            </p>

            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">
                {{ __('products.catalog') }}
            </h1>

            <p class="text-sm text-slate-600">
                {{ __('products.showing_products', ['count' => $total_products]) }}
            </p>
        </div>
    </section>

    <!-- Filters -->
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="GET" action="{{ $listing_route ?? route('user.products.index') }}"
              class="grid gap-3 md:grid-cols-4">

            <!-- Search -->
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('products.search') }}
                </label>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                       placeholder="{{ __('products.search') }}">
            </div>

            <!-- Category -->
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('products.category') }}
                </label>

                <select name="category_id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">

                    <option value="">
                        {{ __('products.all_categories') }}
                    </option>

                    @foreach(($categories ?? []) as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) request('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('products.sort') }}
                </label>

                <select name="sort"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white">

                    <option value="newest"
                        @selected(request('sort', 'newest') === 'newest')>
                        {{ __('products.newest') }}
                    </option>

                    <option value="price_asc"
                        @selected(request('sort') === 'price_asc')>
                        {{ __('products.price_low_high') }}
                    </option>

                    <option value="price_desc"
                        @selected(request('sort') === 'price_desc')>
                        {{ __('products.price_high_low') }}
                    </option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="w-full rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-sky-700">
                    {{ __('products.filter') }}
                </button>

                <a href="{{ $listing_route ?? route('user.products.index') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                    {{ __('products.reset') }}
                </a>
            </div>
        </form>
    </section>

    <!-- Product List -->
    <section class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

            @forelse ($products as $product)
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                    <!-- Image -->
                    <div class="h-44 overflow-hidden rounded-xl bg-slate-100">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="h-full w-full object-cover">
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="mt-4 space-y-2">

                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">
                            {{ $product->category->name ?? __('products.uncategorized') }}
                        </p>

                        <h2 class="text-lg font-semibold text-slate-900">
                            {{ $product->name }}
                        </h2>

                        <p class="text-sm text-slate-600">
                            {{ $product->description ?: __('products.no_description') }}
                        </p>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-base font-semibold text-slate-900">
                                {{ format_price($product->price) }}
                            </span>

                            <a href="{{ route('user.products.show', $product) }}"
                               class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                {{ __('products.view') }}
                            </a>
                        </div>

                    </div>
                </article>

            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-sm text-slate-600">
                    {{ __('products.no_products') }}
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
            {{ $products->links() }}
        </div>
    </section>

    <!-- Recently Viewed -->
    @if (($recentlyViewedProducts ?? collect())->isNotEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">
                    {{ __('products.recently_viewed') }}
                </p>

                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
                    {{ __('products.recent_products') }}
                </h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                @foreach ($recentlyViewedProducts as $recentProduct)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                        <div class="h-40 overflow-hidden rounded-xl bg-slate-100">
                            @if ($recentProduct->image)
                                <img src="{{ asset('storage/' . $recentProduct->image) }}"
                                     alt="{{ $recentProduct->name }}"
                                     class="h-full w-full object-cover">
                            @endif
                        </div>

                        <div class="mt-4 space-y-2">

                            <p class="text-xs uppercase tracking-[0.16em] text-slate-500">
                                {{ $recentProduct->category->name ?? __('products.uncategorized') }}
                            </p>

                            <h3 class="text-lg font-semibold text-slate-900">
                                {{ $recentProduct->name }}
                            </h3>

                            <div class="flex items-center justify-between">
                                <span class="text-base font-semibold text-slate-900">
                                    {{ format_price($recentProduct->price) }}
                                </span>

                                <a href="{{ route('user.products.show', $recentProduct) }}"
                                   class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    {{ __('products.view') }}
                                </a>
                            </div>

                        </div>
                    </article>
                @endforeach

            </div>
        </section>
    @endif

</div>
@endsection
