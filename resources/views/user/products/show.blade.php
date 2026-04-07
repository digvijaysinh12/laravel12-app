@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $stock = (int) ($product->stock ?? 0);
@endphp

<div class="space-y-6" data-product-detail data-product-id="{{ $product->id }}">
    <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="aspect-square bg-slate-100">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-center justify-center text-sm text-slate-500">No image available</div>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $product->name }}</h1>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <span class="text-2xl font-bold text-slate-900">INR {{ number_format($product->price, 2) }}</span>
                <span
                    class="product-stock-badge inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $stock > 10 ? 'bg-emerald-100 text-emerald-700' : ($stock > 0 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}"
                    data-product-id="{{ $product->id }}"
                >
                    {{ $stock > 0 ? "Stock: $stock" : 'Out of Stock' }}
                </span>
            </div>

            <p class="mt-4 text-sm leading-7 text-slate-600">{{ $product->description ?: 'No description available for this product yet.' }}</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('user.products.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                    Back to Products
                </a>

                @if(auth()->check() && auth()->user()->role === 'user')
                    <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="add-to-cart-btn rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-black disabled:cursor-not-allowed disabled:bg-slate-300"
                            data-product-id="{{ $product->id }}"
                            @disabled($stock <= 0)
                        >
                            {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                        </button>
                    </form>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-black">
                        Edit Product
                    </a>
                @endif
            </div>
        </section>
    </div>

    <section x-data="{ tab: 'description' }" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-4">
            <button
                type="button"
                @click="tab = 'description'"
                :class="tab === 'description' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                class="rounded-lg px-4 py-2 text-sm font-medium transition"
            >
                Description
            </button>
            <button
                type="button"
                @click="tab = 'reviews'"
                :class="tab === 'reviews' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                class="rounded-lg px-4 py-2 text-sm font-medium transition"
            >
                Reviews
            </button>
        </div>

        <div class="pt-5 text-sm text-slate-600" x-show="tab === 'description'">
            <p class="leading-7">{{ $product->description ?: 'No detailed description has been added yet.' }}</p>
        </div>

        <div class="pt-5" x-show="tab === 'reviews'">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-medium text-slate-700">Customer feedback is coming soon.</p>
                <p class="mt-1 text-sm text-slate-500">Reviews are not available for this product yet.</p>
            </div>
        </div>
    </section>
</div>
@endsection
