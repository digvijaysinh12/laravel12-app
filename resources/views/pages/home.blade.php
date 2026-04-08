@extends('layouts.app')

@section('title', 'Home')

@section('content')
@php
    $categories = $featuredProducts->pluck('category')->filter()->unique('id')->take(6)->values();
@endphp

<div class="space-y-8">
    <section class="grid gap-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:grid-cols-[1.3fr_0.7fr] lg:p-8">
        <div class="space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Trusted shopping</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                Clean shopping, fast checkout, and live order updates.
            </h1>
            <p class="max-w-2xl text-sm leading-7 text-slate-600">
                Browse featured products, manage your cart, place an order, and track what happens next from one place.
            </p>

            <div class="flex flex-wrap gap-3">
                <a href="{{ auth()->check() ? route('user.products.index') : route('login') }}" class="inline-flex items-center rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                    Shop Products
                </a>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Create Account
                    </a>
                @endguest
            </div>
        </div>

        <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-3 lg:grid-cols-1">
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Featured</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $featuredProducts->count() }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Categories</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $categories->count() }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Load Time</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $loadTimeMs ?? '0' }} ms</p>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Featured Products</h2>
                <p class="mt-1 text-sm text-slate-600">A few curated items to get you started.</p>
            </div>
            <a href="{{ route('user.products.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">
                View all
            </a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500">
                    No featured products are available yet.
                </div>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Shop by Category</h2>
            <p class="mt-1 text-sm text-slate-600">Jump into the collection that fits what you need.</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($categories as $category)
                <a href="{{ auth()->check() ? route('user.products.category', $category->id) : route('login') }}" class="rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300 hover:bg-white">
                    <p class="font-medium text-slate-900">{{ $category->name }}</p>
                    <p class="mt-1 text-sm text-slate-500">Browse products in this category</p>
                </a>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center text-sm text-slate-500">
                    Categories will appear once products are added.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
