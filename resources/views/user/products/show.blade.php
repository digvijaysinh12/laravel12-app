@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $stock = (int) ($product->stock ?? 0);
@endphp

<div class="space-y-6">
    <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="aspect-square bg-slate-50">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-center justify-center text-sm text-slate-500">No image available</div>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $product->name }}</h1>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <span class="text-2xl font-semibold text-slate-900">INR {{ number_format($product->price, 2) }}</span>
                <span class="product-stock-badge inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}" data-product-id="{{ $product->id }}">
                    {{ $stock > 0 ? "Stock: $stock" : 'Out of Stock' }}
                </span>
            </div>

            <p class="mt-4 text-sm leading-7 text-slate-600">
                {{ $product->description ?: 'No description available for this product yet.' }}
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('user.products.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Back to Products
                </a>

                @if (auth()->check() && auth()->user()->role === 'user')
                    <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="add-to-cart-btn inline-flex items-center rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                            data-product-id="{{ $product->id }}"
                            @disabled($stock <= 0)
                        >
                            {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                        </button>
                    </form>
                @endif

            </div>
        </section>
    </div>
</div>
@endsection
