@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h4 class="mb-4">Shopping Cart</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(count($products) > 0)

    <div class="row">

        {{-- LEFT: CART ITEMS --}}
        <div class="col-md-8">

            <div class="card">
                <div class="card-body p-0">

                    @php $grandTotal = 0; @endphp

                    @foreach($products as $product)
                        @php
                            $qty = $cart[$product->id] ?? 0;
                            $total = $product->price * $qty;
                            $grandTotal += $total;
                        @endphp

                        <div class="d-flex justify-content-between align-items-center border-bottom p-3">

                            <div>
                                <div>{{ $product->name }}</div>
                                <small class="text-muted">
                                    ₹{{ number_format($product->price) }} × {{ $qty }}
                                </small>
                            </div>

                            <div class="text-end">
                                <div>₹{{ number_format($total) }}</div>

                                <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>

        </div>

        {{-- RIGHT: SUMMARY --}}
        <div class="col-md-4">

            <div class="card">
                <div class="card-body">

                    <h5 class="mb-3">Summary</h5>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Total</span>
                        <strong>₹{{ number_format($grandTotal) }}</strong>
                    </div>

                    <button class="btn btn-dark w-100 mb-2">
                        Checkout
                    </button>

                    <a href="{{ route('products.index') }}" 
                       class="btn btn-outline-secondary w-100 mb-2">
                        Continue Shopping
                    </a>

                    {{-- ❗ FIXED: Clear Cart (should NOT be GET) --}}
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger w-100">
                            Clear Cart
                        </button>
                    </form>

                </div>
            </div>

        </div>

    </div>

    @else

        {{-- EMPTY --}}
        <div class="text-center py-5">
            <h5>Your cart is empty</h5>
            <a href="{{ route('products.index') }}" class="btn btn-dark mt-3">
                Browse Products
            </a>
        </div>

    @endif

</div>
@endsection