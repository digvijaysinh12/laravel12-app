@extends('user.layouts.app')

@section('title', 'Cart')

@section('content')
<div class="space-y-6 js-cart-page" data-products-url="{{ route('user.products.index') }}">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Shopping cart</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Your Cart</h1>
                <p class="mt-2 text-sm text-slate-600">Review items before moving to checkout.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('user.products.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Continue Shopping
                </a>
                <a href="{{ route('user.checkout.index') }}" class="inline-flex items-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-sky-700">
                    Checkout
                </a>
            </div>
        </div>
    </section>

    @if (!empty($cart))
        <div class="grid gap-6 lg:grid-cols-[1.5fr_0.9fr]">
            <section class="space-y-4" data-cart-list>
                @foreach ($cart as $productId => $item)
                    @php $lineTotal = $item['price'] * $item['quantity']; @endphp
                    <article class="cart-row rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" data-id="{{ $productId }}" data-price="{{ $item['price'] }}">
                        <div class="flex gap-4">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-slate-50">
                                @if (!empty($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="text-base font-semibold text-slate-900">{{ $item['name'] }}</h2>
                                <p class="mt-1 text-sm text-slate-600">INR {{ number_format($item['price'], 2) }}</p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <form method="POST" action="{{ route('user.cart.decrement', $productId) }}" class="inline-flex" data-cart-form data-cart-action="decrement">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-sm text-slate-700 transition hover:bg-slate-50" aria-label="Decrease quantity">-</button>
                                    </form>

                                    <span class="qty inline-flex min-w-10 items-center justify-center rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-900">
                                        {{ $item['quantity'] }}
                                    </span>

                                    <form method="POST" action="{{ route('user.cart.increment', $productId) }}" class="inline-flex" data-cart-form data-cart-action="increment">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-sm text-slate-700 transition hover:bg-slate-50" aria-label="Increase quantity">+</button>
                                    </form>

                                    <form method="POST" action="{{ route('user.cart.remove', $productId) }}" class="ml-auto inline-flex" data-cart-form data-cart-action="remove">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-rose-600 transition hover:text-rose-700">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="item-total text-right text-base font-semibold text-slate-900">
                                INR {{ number_format($lineTotal, 2) }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-24 lg:h-fit" data-cart-summary>
                <h2 class="text-lg font-semibold text-slate-900">Order Summary</h2>

                <div class="mt-4 space-y-3 border-t border-slate-200 pt-4 text-sm text-slate-700">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span id="cart-subtotal">INR {{ number_format($summary['subtotal'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Tax ({{ $summary['tax_percent'] ?? 5 }}%)</span>
                        <span id="cart-tax">INR {{ number_format($summary['tax'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Shipping</span>
                        <span id="cart-shipping">INR {{ number_format($shipping['amount'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-900">
                        <span>Total</span>
                        <span id="cart-grand-total">INR {{ number_format($grandTotal ?? 0, 2) }}</span>
                    </div>
                </div>

                <a href="{{ route('user.checkout.index') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                    Proceed to Checkout
                </a>

                <form method="POST" action="{{ route('user.cart.clear') }}" class="mt-3" data-cart-form data-cart-action="clear">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Clear Cart
                    </button>
                </form>
            </aside>
        </div>
    @else
        <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm" data-empty-state>
            <h2 class="text-xl font-semibold text-slate-900">Your cart is empty</h2>
            <p class="mt-2 text-sm text-slate-600">Add products to continue shopping.</p>
            <a href="{{ route('user.products.index') }}" class="mt-5 inline-flex rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-sky-700">
                Browse Products
            </a>
        </section>
    @endif
</div>
@endsection
