<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Checkout</h1>
                <p class="mt-1 text-sm text-slate-600">Confirm shipping details and place your order.</p>
            </div>
            <a href="{{ route('user.cart.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                Back to Cart
            </a>
        </div>
    </section>

    @if (empty($cart))
        <section class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">No items available for checkout</h2>
            <p class="mt-2 text-sm text-slate-600">Your cart is empty. Add products to continue.</p>
            <a href="{{ route('user.products.index') }}" class="mt-5 inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black">
                Browse Products
            </a>
        </section>
    @else
        <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Shipping Address</h2>
                <p class="mt-1 text-sm text-slate-600">Provide delivery information for your order.</p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Full Name</label>
                        <input type="text" value="{{ auth()->user()->name ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
                        <input type="text" placeholder="Enter phone number" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Address Line</label>
                        <input type="text" placeholder="House no, street, locality" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">City</label>
                        <input type="text" placeholder="City" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Postal Code</label>
                        <input type="text" placeholder="Postal code" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Order Notes</label>
                        <textarea rows="4" placeholder="Optional delivery instructions" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200"></textarea>
                    </div>
                </div>
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

                <form action="{{ route('user.checkout') }}" method="POST" class="mt-5">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-black">
                        Place Order
                    </button>
                </form>
            </aside>
        </div>
    @endif
</div>
