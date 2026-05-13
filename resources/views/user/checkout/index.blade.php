@extends('user.layouts.app')

@section('title', 'Checkout')

@section('content')

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- Checkout Header --}}
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-sky-600">
                    Secure Checkout
                </p>

                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                    Complete Your Order
                </h1>

                <p class="mt-2 text-sm leading-relaxed text-slate-500">
                    Fast delivery, secure payments and easy returns.
                </p>
            </div>

            <a href="{{ route('user.cart.index') }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                ← Back to Cart
            </a>
        </div>

        {{-- Checkout Steps --}}
        <div class="mt-8 flex items-center gap-4 overflow-x-auto text-sm font-medium">

            <div class="flex items-center gap-2 text-sky-600">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-100">
                    ✓
                </div>
                Cart
            </div>

            <div class="h-px w-12 bg-slate-300"></div>

            <div class="flex items-center gap-2 text-sky-600">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-600 text-white">
                    2
                </div>
                Checkout
            </div>

            <div class="h-px w-12 bg-slate-300"></div>

            <div class="flex items-center gap-2 text-slate-400">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-200">
                    3
                </div>
                Payment
            </div>
        </div>
    </section>

    @if (empty($cart))

        {{-- Empty Cart --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm">

            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-4xl">
                🛒
            </div>

            <h2 class="mt-6 text-2xl font-bold text-slate-900">
                Your cart is empty
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Looks like you haven’t added anything yet.
            </p>

            <a href="{{ route('user.products.index') }}"
               class="mt-6 inline-flex items-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                Browse Products
            </a>
        </section>

    @else

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">

            {{-- Checkout Form --}}
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-900">
                        Delivery Information
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Enter your shipping details below.
                    </p>
                </div>

                <form method="POST"
                      action="{{ route('user.checkout.store') }}"
                      class="space-y-5">

                    @csrf

                    {{-- Full Name --}}
                    <div>
                        <label for="name"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Full Name
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', auth()->user()->name ?? '') }}"
                            placeholder="Enter your full name"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Phone Number
                        </label>

                        <input
                            id="phone"
                            type="tel"
                            inputmode="numeric"
                            maxlength="10"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="Enter 10 digit mobile number"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                        >

                        @error('phone')
                            <p class="mt-2 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="address"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Delivery Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="4"
                            placeholder="House no, street, area..."
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                        >{{ old('address') }}</textarea>

                        @error('address')
                            <p class="mt-2 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- City + Pincode --}}
                    <div class="grid gap-5 sm:grid-cols-2">

                        <div>
                            <label for="city"
                                   class="mb-2 block text-sm font-semibold text-slate-700">
                                City
                            </label>

                            <input
                                id="city"
                                type="text"
                                name="city"
                                value="{{ old('city') }}"
                                placeholder="Ahmedabad"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                            >
                        </div>

                        <div>
                            <label for="pincode"
                                   class="mb-2 block text-sm font-semibold text-slate-700">
                                Pincode
                            </label>

                            <input
                                id="pincode"
                                type="text"
                                inputmode="numeric"
                                maxlength="6"
                                name="pincode"
                                value="{{ old('pincode') }}"
                                placeholder="380001"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                            >
                        </div>
                    </div>

                    {{-- Order Notes --}}
                    <div>
                        <label for="notes"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Order Notes
                            <span class="text-slate-400">(Optional)</span>
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            placeholder="Any delivery instructions?"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                        >{{ old('notes') }}</textarea>
                    </div>

                    {{-- Cart Items Count --}}
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        {{ trans_choice('cart_items', $summary['item_count'], ['count' => $summary['item_count']]) }}
                    </div>

                    {{-- Place Order --}}
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-sky-600 px-5 py-4 text-base font-semibold text-white shadow-lg transition hover:bg-sky-700 hover:shadow-xl">
                        Place Order • INR {{ number_format($grandTotal ?? 0, 2) }}
                    </button>

                    {{-- Trust Badges --}}
                    <div class="flex flex-wrap items-center justify-center gap-4 pt-2 text-xs text-slate-500">
                        <span>🔒 Secure Checkout</span>
                        <span>💵 Cash on Delivery</span>
                        <span>🚚 Fast Delivery</span>
                    </div>
                </form>
            </section>

            {{-- Order Summary --}}
            <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-24 lg:h-fit">

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Order Summary
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $summary['item_count'] }} items in cart
                        </p>
                    </div>
                </div>

                {{-- Products --}}
                <div class="mt-6 space-y-4">

                    @foreach ($cart as $item)

                        <div class="flex items-start gap-4 rounded-2xl border border-slate-100 p-3">

                            {{-- Product Image --}}
                            <div class="h-16 w-16 overflow-hidden rounded-xl bg-slate-100">
                                <img
                                    src="{{ $item['image'] ?? 'https://via.placeholder.com/150' }}"
                                    alt="{{ $item['name'] }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>

                            {{-- Product Info --}}
                            <div class="flex-1">

                                <h3 class="text-sm font-semibold text-slate-800">
                                    {{ $item['name'] }}
                                </h3>

                                <div class="mt-2 flex items-center justify-between">

                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                        Qty {{ $item['quantity'] }}
                                    </span>

                                    <p class="text-sm font-semibold text-slate-900">
                                        INR {{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>

                {{-- Coupon --}}
                <div class="mt-6">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Coupon Code
                    </label>

                    <div class="flex gap-2">
                        <input
                            type="text"
                            placeholder="Enter coupon"
                            class="flex-1 rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                        >

                        <button
                            type="button"
                            class="rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Apply
                        </button>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="mt-6 space-y-4 border-t border-slate-200 pt-5 text-sm">

                    <div class="flex items-center justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>INR {{ number_format($summary['subtotal'] ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-slate-600">
                        <span>Tax ({{ $summary['tax_percent'] ?? 5 }}%)</span>
                        <span>INR {{ number_format($summary['tax'] ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-slate-600">
                        <span>Shipping</span>
                        <span>INR {{ number_format($shipping['amount'] ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-200 pt-4 text-lg font-bold text-slate-900">
                        <span>Total</span>
                        <span>INR {{ number_format($grandTotal ?? 0, 2) }}</span>
                    </div>
                </div>

                {{-- Delivery ETA --}}
                <div class="mt-6 rounded-2xl bg-sky-50 p-4">
                    <p class="text-sm font-semibold text-sky-700">
                        🚚 Estimated Delivery
                    </p>

                    <p class="mt-1 text-sm text-sky-600">
                        2 - 4 business days
                    </p>
                </div>
            </aside>
        </div>

    @endif
</div>

@endsection