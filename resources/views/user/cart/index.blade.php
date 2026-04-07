@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
@php
    $grandTotal = 0;
@endphp

<div class="space-y-6 js-cart-page" data-products-url="{{ route('user.products.index') }}">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Shopping Cart</h1>
        <p class="mt-1 text-sm text-slate-600">Review your items and proceed to checkout.</p>
    </section>

    @if (!empty($cart))
        <div class="grid gap-6 lg:grid-cols-[1.5fr_0.9fr]">
            <section class="space-y-4" data-cart-list>
                @foreach ($cart as $productId => $item)
                    @php
                        $lineTotal = $item['price'] * $item['quantity'];
                        $grandTotal += $lineTotal;
                    @endphp

                    <article class="cart-row rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5" data-id="{{ $productId }}" data-price="{{ $item['price'] }}">
                        <div class="flex gap-4">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-md bg-slate-100 sm:h-24 sm:w-24">
                                @if (!empty($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-xs text-slate-500">No image</div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="text-base font-semibold text-slate-900">{{ $item['name'] }}</h2>
                                <p class="mt-1 text-sm text-slate-600">INR {{ number_format($item['price'], 2) }}</p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        class="btn-dec inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 text-sm text-slate-700 hover:bg-slate-100"
                                        data-id="{{ $productId }}"
                                        data-url="{{ route('user.cart.decrement', $productId) }}"
                                        aria-label="Decrease quantity"
                                    >
                                        -
                                    </button>

                                    <span class="qty inline-flex min-w-10 items-center justify-center rounded-md border border-slate-200 px-2 py-1 text-sm font-medium text-slate-900">
                                        {{ $item['quantity'] }}
                                    </span>

                                    <button
                                        type="button"
                                        class="btn-inc inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 text-sm text-slate-700 hover:bg-slate-100"
                                        data-id="{{ $productId }}"
                                        data-url="{{ route('user.cart.increment', $productId) }}"
                                        aria-label="Increase quantity"
                                    >
                                        +
                                    </button>

                                    <button
                                        type="button"
                                        class="btn-remove ml-auto text-sm font-medium text-rose-600 hover:text-rose-700"
                                        data-id="{{ $productId }}"
                                        data-url="{{ route('user.cart.remove', $productId) }}"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <div class="item-total text-right text-base font-semibold text-slate-900">
                                INR {{ number_format($lineTotal, 2) }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <aside class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-slate-900">Order Summary</h2>

                <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 text-base font-semibold text-slate-900">
                    <span>Total</span>
                    <span id="cart-grand-total">INR {{ number_format($grandTotal, 2) }}</span>
                </div>

                <form action="{{ route('user.checkout') }}" method="POST" class="mt-5">
                    @csrf
                    <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-black">
                        Checkout
                    </button>
                </form>

                <button
                    type="button"
                    class="btn-clear mt-3 w-full rounded-md border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                    data-url="{{ route('user.cart.clear') }}"
                >
                    Clear Cart
                </button>
            </aside>
        </div>
    @else
        <section class="rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm" data-empty-state>
            <h2 class="text-xl font-semibold text-slate-900">Your cart is empty</h2>
            <p class="mt-2 text-sm text-slate-600">Add products to continue shopping.</p>
            <a href="{{ route('user.products.index') }}" class="mt-5 inline-flex rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black">
                Browse Products
            </a>
        </section>
    @endif
</div>
@endsection
