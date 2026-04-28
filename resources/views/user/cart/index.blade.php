@extends('user.layouts.app')

@section('title', 'Cart')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 border border-green-300">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 border border-red-300">
        ❌ {{ session('error') }}
    </div>
@endif
{{ __('Welcome User', ['name' => auth()->user()->name]) }}
<div class="max-w-5xl mx-auto px-4 py-8">
<p class="text-sm text-gray-500">
    {{ trans_choice('cart_items', $summary['item_count'], ['count' => $summary['item_count']]) }}
</p>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-black">{{__('Your Cart')}}</h2>
            <p class="text-sm text-gray-500">Review items before checkout</p>
        </div>

        <a href="{{ route('user.products.index') }}"
           class="text-sm text-black border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition">
            {{ __('Continue Shopping') }}
        </a>
    </div>

    @if (!empty($cart))

        {{-- Cart Items --}}
        <div class="space-y-4">
            @foreach ($cart as $productId => $item)
                @php $lineTotal = $item['price'] * $item['quantity']; @endphp

                <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between">

                    {{-- Product Info --}}
                    <div>
                        <h3 class="font-medium text-black">{{ $item['name'] }}</h3>
                        <p class="text-sm text-gray-500">₹{{ number_format($item['price'], 2) }}</p>
                    </div>

                    {{-- Quantity Controls --}}
                    <div class="flex items-center gap-2">

                        <form method="POST" action="{{ route('user.cart.decrement', $productId) }}">
                            @csrf
                            <button class="w-8 h-8 border border-gray-300 rounded-md hover:bg-gray-100">-</button>
                        </form>

                        <span class="px-3">{{ $item['quantity'] }}</span>

                        <form method="POST" action="{{ route('user.cart.increment', $productId) }}">
                            @csrf
                            <button class="w-8 h-8 border border-gray-300 rounded-md hover:bg-gray-100">+</button>
                        </form>

                    </div>

                    {{-- Price --}}
                    <div class="text-right">
                        <p class="font-semibold text-black">
                            ₹{{ number_format($lineTotal, 2) }}
                        </p>

                        <form method="POST" action="{{ route('user.cart.remove', $productId) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-gray-500 hover:text-black mt-1">
                                {{ __('Remove') }}
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div class="mt-8 border border-gray-200 rounded-xl p-6">

            <h3 class="text-lg font-semibold mb-4">{{ __('Order Summary') }}</h3>

            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>{{ __('Subtotal') }}</span>
                    <span>₹{{ number_format($summary['subtotal'] ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between">
                    <span>{{ __('Tax') }}</span>
                    <span>₹{{ number_format($summary['tax'] ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between">
                    <span>{{ __('Shipping') }}</span>
                    <span>₹{{ number_format($shipping['amount'] ?? 0, 2) }}</span>
                </div>
            </div>

            <div class="border-t mt-4 pt-4 flex justify-between font-semibold text-black">
                <span>{{ __('Total') }}</span>
                <span>₹{{ number_format($grandTotal ?? 0, 2) }}</span>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex gap-3">

                <a href="{{ route('user.checkout.index') }}"
                   class="flex-1 text-center bg-black text-white py-2 rounded-lg hover:bg-gray-800 transition">
                    {{ __('Proceed to Checkout') }}
                </a>

                <form method="POST" action="{{ route('user.cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition">
                        {{ __('Clear') }}
                    </button>
                </form>

            </div>

        </div>

    @else

        {{-- Empty State --}}
        <div class="text-center py-20 border border-dashed border-gray-300 rounded-xl">
            <p class="text-gray-500 mb-4">{{ __('Your cart is empty') }}</p>

            <a href="{{ route('user.products.index') }}"
               class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition">
                {{ __('Browse Products') }}
            </a>
        </div>

    @endif

</div>
@endsection
