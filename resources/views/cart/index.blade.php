@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h3 class="mb-4 fw-bold">Shopping Cart</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(count($products) > 0)

    <div class="row">

        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-body p-0">

                    @php $grandTotal = 0; @endphp

                    @foreach($products as $product)
                        @php
                            $qty = $cart[$product->id] ?? 0;
                            $total = $product->price * $qty;
                            $grandTotal += $total;
                        @endphp

                        <div class="d-flex align-items-center border-bottom p-3">

                            {{-- Product Info --}}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-semibold">
                                    {{ $product->name }}
                                </h6>

                                <small class="text-muted">
                                    ₹{{ number_format($product->price) }} each
                                </small>

                                <div class="mt-2">
                                    <span class="badge bg-light text-dark">
                                        Qty: {{ $qty }}
                                    </span>
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="text-end me-3">
                                <strong>₹{{ number_format($total) }}</strong>
                            </div>

                            {{-- Remove --}}
                            <div>
                                <a href="{{ route('cart.remove', $product->id) }}"
                                   class="btn btn-outline-danger btn-sm">
                                    Remove
                                </a>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>

        </div>

        {{-- RIGHT: SUMMARY --}}
        <div class="col-md-4">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h5 class="mb-3">Order Summary</h5>



                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong>₹{{ number_format($grandTotal) }}</strong>
                    </div>

                    <button class="btn btn-dark w-100 mb-2">
                        Proceed to Checkout
                    </button>

                    <a href="{{ route('products.index') }}" 
                       class="btn btn-outline-secondary w-100">
                        Continue Shopping
                    </a>

                    <hr>

                    <a href="{{ route('cart.clear') }}" 
                       class="btn btn-outline-danger w-100">
                        Clear Cart
                    </a>

                </div>
            </div>

        </div>

    </div>

    @else

        {{-- EMPTY STATE --}}
        <div class="text-center py-5">
            <h5 class="mb-3">Your cart is empty</h5>
            <p class="text-muted">Looks like you haven’t added anything yet.</p>

            <a href="{{ route('products.index') }}" 
               class="btn btn-dark">
                Start Shopping
            </a>
        </div>

    @endif

</div>
@endsection