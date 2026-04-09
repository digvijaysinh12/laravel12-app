@extends('user.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Checkout</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Place your order</h1>
                <p class="mt-2 text-sm text-slate-600">Confirm delivery details and complete checkout.</p>
            </div>

            <a href="{{ route('user.cart.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Back to Cart
            </a>
        </div>
    </section>

    @if (empty($cart))
        <section class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">No items available for checkout</h2>
            <p class="mt-2 text-sm text-slate-600">Your cart is empty. Add products to continue.</p>
            <a href="{{ route('user.products.index') }}" class="mt-5 inline-flex rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-sky-700">
                Browse Products
            </a>
        </section>
    @else
        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Shipping Details</h2>
                <p class="mt-1 text-sm text-slate-600">Use the address where you want the order delivered.</p>

                <form method="POST" action="{{ route('user.checkout.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="address" class="mb-1 block text-sm font-medium text-slate-700">Address</label>
                        <textarea id="address" name="address" rows="4" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400">{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400">
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                        Place Order
                    </button>
                </form>
            </section>

            <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-24 lg:h-fit">
                <h2 class="text-lg font-semibold text-slate-900">Order Summary</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($cart as $item)
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <div>
                                <p class="font-medium text-slate-800">{{ $item['name'] }}</p>
                                <p class="text-slate-500">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="font-medium text-slate-700">INR {{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 space-y-3 border-t border-slate-200 pt-4 text-sm text-slate-700">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span>INR {{ number_format($summary['subtotal'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Tax ({{ $summary['tax_percent'] ?? 5 }}%)</span>
                        <span>INR {{ number_format($summary['tax'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Shipping</span>
                        <span>INR {{ number_format($shipping['amount'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-900">
                        <span>Total</span>
                        <span>INR {{ number_format($grandTotal ?? 0, 2) }}</span>
                    </div>
                </div>
            </aside>
        </div>
    @endif
</div>
@endsection
