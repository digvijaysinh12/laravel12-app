@extends('layouts.app')

@section('content')
    <div class="py-5 max-w-1100 mx-auto">
        <h4 class="fw-semibold mb-4">Shopping Cart</h4>

        @if ($cart && count($cart) > 0)
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="border rounded-3 bg-white">
                        @php $grandTotal = 0; @endphp
                        @foreach ($cart as $productId => $item)
                            @php
                                $total = $item['price'] * $item['quantity'];
                                $grandTotal += $total;
                            @endphp

                            <div class="d-flex align-items-center p-3 border-bottom cart-row" data-id="{{ $productId }}" data-price="{{ $item['price'] }}">
                                <img src="{{ asset('storage/' . $item['image']) }}" width="70" height="70" class="rounded object-cover">

                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-medium">{{ $item['name'] }}</div>
                                    <small class="text-muted">Rs. {{ number_format($item['price'], 2) }}</small>

                                    <div class="d-flex align-items-center mt-2 gap-2">
                                        <button class="btn btn-sm btn-light border btn-dec" data-id="{{ $productId }}">-</button>
                                        <span class="qty">{{ $item['quantity'] }}</span>
                                        <button class="btn btn-sm btn-light border btn-inc" data-id="{{ $productId }}">+</button>
                                        <button class="btn btn-sm text-muted btn-remove" data-id="{{ $productId }}">Remove</button>
                                    </div>
                                </div>

                                <div class="fw-medium item-total">
                                    Rs. {{ number_format($total, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded-3 p-4 bg-white">
                        <h5 class="fw-semibold mb-3">Order Summary</h5>

                        <div id="summary-items">
                            @foreach ($cart as $productId => $item)
                                <div class="d-flex justify-content-between small mb-2 summary-item" data-id="{{ $productId }}">
                                    <span>
                                        {{ $item['name'] }} × <span class="summary-qty">{{ $item['quantity'] }}</span>
                                    </span>
                                    <span class="summary-price">
                                        Rs. {{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4 fw-semibold">
                            <span>Total</span>
                            <span id="final-total">Rs. {{ number_format($grandTotal, 2) }}</span>
                        </div>

                        <form action="{{ route('checkout') }}" method="POST" class="d-grid gap-2">
                            @csrf
                            <x-button class="btn-dark w-100">Checkout</x-button>
                        </form>

                        <form action="{{ route('cart.clear') }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <x-button class="w-100" variant="ghost">Clear Cart</x-button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <h5>Your cart is empty</h5>
            </div>
        @endif
    </div>
@endsection
