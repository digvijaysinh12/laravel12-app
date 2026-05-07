@extends('user.layouts.app')

@section('title', __('products.catalog'))

@section('content')

<div class="mx-auto max-w-7xl space-y-8">

    <!-- Header -->
    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-900 px-8 py-10 text-white shadow-sm">

        <div class="max-w-2xl">

            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                {{ __('products.catalog') }}
            </p>

            <h1 class="mt-3 text-4xl font-semibold tracking-tight">
                {{ __('products.catalog') }}
            </h1>

            <p class="mt-4 text-sm leading-relaxed text-slate-3     00">
                {{ __('products.showing_products', ['count' => $total_products]) }}
            </p>

        </div>

    </section>

    <!-- Filters -->
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

        <form method="GET"
              action="{{ $listing_route ?? route('user.products.index') }}"
              class="grid gap-4 lg:grid-cols-4">

            <!-- Search -->
            <div>

                <label class="mb-2 block text-sm font-medium text-slate-700">
                    {{ __('products.search') }}
                </label>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('products.search') }}"
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm transition focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">

            </div>

            <!-- Category -->
            <div>

                <label class="mb-2 block text-sm font-medium text-slate-700">
                    {{ __('products.category') }}
                </label>

                <select name="category_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm transition focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">

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

                <label class="mb-2 block text-sm font-medium text-slate-700">
                    {{ __('products.sort') }}
                </label>

                <select name="sort"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm transition focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200">

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
            <div class="flex items-end gap-3">

                <button type="submit"
                        class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-medium text-white transition hover:bg-black">

                    {{ __('products.filter') }}

                </button>

                <a href="{{ $listing_route ?? route('user.products.index') }}"
                   class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50">

                    {{ __('products.reset') }}

                </a>

            </div>

        </form>

    </section>

    <!-- Products -->
    <section>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">

            @forelse ($products as $product)

                <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                    <!-- Product Image -->
                    <div class="overflow-hidden rounded-2xl bg-slate-100">

                        @if ($product->image)

                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="h-60 w-full object-cover transition duration-500 group-hover:scale-105">

                        @endif

                    </div>

                    <!-- Product Content -->
                    <div class="mt-5">

                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">
                            {{ $product->category->name ?? __('products.uncategorized') }}
                        </p>

                        <h2 class="mt-2 text-lg font-semibold tracking-tight text-slate-900">
                            {{ $product->name }}
                        </h2>

                        <p class="mt-3 text-sm leading-relaxed text-slate-600">
                            {{ \Illuminate\Support\Str::limit($product->description ?: __('products.no_description'), 80) }}
                        </p>

                        <div class="mt-5 flex items-center justify-between">

                            <span class="text-lg font-semibold text-slate-900">
                                {{ format_price($product->price) }}
                            </span>

                            <a href="{{ route('user.products.show', $product) }}"
                               class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">

                                {{ __('products.view') }}

                            </a>

                        </div>

                    </div>

                </article>

            @empty

                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center">

                    <h3 class="text-lg font-semibold text-slate-900">
                        {{ __('products.no_products') }}
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        Try changing filters or search keywords.
                    </p>

                </div>

            @endforelse

        </div>

        <!-- Pagination -->
        <div class="pt-8">
            {{ $products->links() }}
        </div>

    </section>

    <!-- Recently Viewed -->
    @if (($recentlyViewedProducts ?? collect())->isNotEmpty())

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-6">

                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
                    {{ __('products.recently_viewed') }}
                </p>

                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
                    {{ __('products.recent_products') }}
                </h2>

            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($recentlyViewedProducts as $recentProduct)

                    <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                        <div class="overflow-hidden rounded-2xl bg-slate-100">

                            @if ($recentProduct->image)

                                <img src="{{ asset('storage/' . $recentProduct->image) }}"
                                     alt="{{ $recentProduct->name }}"
                                     class="h-52 w-full object-cover transition duration-500 group-hover:scale-105">

                            @endif

                        </div>

                        <div class="mt-5">

                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">
                                {{ $recentProduct->category->name ?? __('products.uncategorized') }}
                            </p>

                            <h3 class="mt-2 text-lg font-semibold tracking-tight text-slate-900">
                                {{ $recentProduct->name }}
                            </h3>

                            <div class="mt-5 flex items-center justify-between">

                                <span class="text-lg font-semibold text-slate-900">
                                    {{ format_price($recentProduct->price) }}
                                </span>

                                <a href="{{ route('user.products.show', $recentProduct) }}"
                                   class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">

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
