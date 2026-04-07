@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
@php
    $featuredProducts = $featuredProducts ?? collect();
    $categories = $featuredProducts->pluck('category')->filter()->unique('id')->take(6)->values();
    $newArrivals = $featuredProducts->sortByDesc('id')->take(4)->values();
    $offerProducts = $featuredProducts->where('price', '>=', 500)->take(4)->values();
    if ($offerProducts->isEmpty()) {
        $offerProducts = $featuredProducts->take(4)->values();
    }
@endphp

<div class="space-y-10">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-8 p-8 sm:p-10 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Trusted online shopping</p>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Shop Everyday Essentials With Confidence
                </h1>
                <p class="max-w-2xl text-sm leading-7 text-slate-600">
                    Discover curated products, transparent pricing, and a checkout flow designed for speed and reliability.
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        <a
                            href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.products.index') }}"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-black"
                        >
                            Start Shopping
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-black">
                            Create Account
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                            Login
                        </a>
                    @endauth
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                <p class="text-sm font-semibold text-slate-900">Store highlights</p>
                <dl class="mt-5 grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                    <div class="rounded-xl bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-[0.14em] text-slate-500">Featured Products</dt>
                        <dd class="mt-2 text-2xl font-semibold text-slate-900">{{ $featuredProducts->count() }}</dd>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-[0.14em] text-slate-500">Category Variety</dt>
                        <dd class="mt-2 text-2xl font-semibold text-slate-900">{{ $categories->count() }}</dd>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-[0.14em] text-slate-500">Fast Load</dt>
                        <dd class="mt-2 text-2xl font-semibold text-slate-900">{{ $loadTimeMs ?? '0' }} ms</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Featured Products</h2>
                <p class="mt-1 text-sm text-slate-600">Top picks selected for value and quality.</p>
            </div>
            @auth
                <a href="{{ route('user.products.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">View all products</a>
            @endauth
        </div>

        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500">
                    Featured products are not available right now.
                </div>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Shop By Category</h2>
            <p class="mt-1 text-sm text-slate-600">Browse product groups designed around your daily needs.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($categories as $category)
                <a
                    href="{{ auth()->check() ? route('user.products.category', $category->id) : route('login') }}"
                    class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300 hover:bg-white"
                >
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-white text-slate-600 shadow-sm">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-slate-900">{{ $category->name }}</p>
                        <p class="text-sm text-slate-500">Explore collection</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center text-sm text-slate-500">
                    Categories will appear when products are available.
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-slate-900">New Arrivals</h2>
                <p class="mt-1 text-sm text-slate-600">Recently added products in our catalog.</p>
            </div>
            <div class="space-y-3">
                @forelse ($newArrivals as $product)
                    <a href="{{ auth()->check() ? route('user.products.show', $product->id) : route('login') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:bg-slate-50">
                        <div class="h-14 w-14 overflow-hidden rounded-lg bg-slate-100">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-slate-900">{{ $product->name }}</p>
                            <p class="text-sm text-slate-600">INR {{ number_format($product->price, 2) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No new arrivals at the moment.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-slate-900">Offers & Discounts</h2>
                <p class="mt-1 text-sm text-slate-600">Limited-time pricing on selected products.</p>
            </div>
            <div class="space-y-3">
                @forelse ($offerProducts as $product)
                    @php
                        $discount = 10 + (($product->id ?? 0) % 3) * 5;
                    @endphp
                    <a href="{{ auth()->check() ? route('user.products.show', $product->id) : route('login') }}" class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3 transition hover:bg-white">
                        <div>
                            <p class="font-medium text-slate-900">{{ $product->name }}</p>
                            <p class="text-sm text-slate-600">Now at INR {{ number_format($product->price, 2) }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ $discount }}% OFF
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No active offers currently.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
