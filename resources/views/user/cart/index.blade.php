@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <section class="rounded-2xl border bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold">Shopping Cart</h1>
    </section>

    @if ($cart && count($cart) > 0)

        @php $grandTotal = 0; @endphp

        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">

            <!-- Cart Items -->
            <section class="space-y-4">

                @foreach ($cart as $productId => $item)

                    @php
                        $total = $item['price'] * $item['quantity'];
                        $grandTotal += $total;
                    @endphp

                    <div class="flex gap-4 bg-white p-4 rounded shadow">

                        <img src="{{ asset('storage/' . $item['image']) }}"
                             class="h-20 w-20 object-cover rounded">

                        <div class="flex-1">

                            <h2 class="font-semibold">{{ $item['name'] }}</h2>
                            <p class="text-sm text-gray-500">
                                ₹{{ number_format($item['price'], 2) }}
                            </p>

                            <div class="flex items-center gap-3 mt-3">

                                <button class="btn-dec px-2 border" data-id="{{ $productId }}">-</button>

                                <span>{{ $item['quantity'] }}</span>

                                <button class="btn-inc px-2 border" data-id="{{ $productId }}">+</button>

                                <button class="btn-remove text-red-500 text-sm" data-id="{{ $productId }}">
                                    Remove
                                </button>

                            </div>

                        </div>

                        <div class="font-semibold">
                            ₹{{ number_format($total, 2) }}
                        </div>

                    </div>

                @endforeach

            </section>

            <!-- Summary -->
            <aside class="bg-white p-6 rounded shadow">

                <h2 class="text-lg font-semibold mb-4">Order Summary</h2>

                <div class="flex justify-between mb-4">
                    <span>Total</span>
                    <span>₹{{ number_format($grandTotal, 2) }}</span>
                </div>

                <!-- ✅ FIXED ROUTES -->
                <form action="{{ route('user.checkout') }}" method="POST">
                    @csrf
                    <button class="w-full bg-black text-white py-2 rounded">
                        Checkout
                    </button>
                </form>

                <form action="{{ route('user.cart.clear') }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button class="w-full border py-2 rounded">
                        Clear Cart
                    </button>
                </form>

            </aside>

        </div>

    @else

        <!-- Empty Cart -->
        <div class="text-center bg-white p-10 rounded shadow">
            <h2 class="text-xl font-semibold">Your cart is empty</h2>

            <a href="{{ route('user.products.index') }}"
               class="inline-block mt-4 bg-black text-white px-4 py-2 rounded">
               Browse Products
            </a>
        </div>

    @endif

</div>

@endsection