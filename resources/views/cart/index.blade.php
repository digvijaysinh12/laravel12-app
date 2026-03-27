@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width: 1100px;">

    <h4 class="fw-semibold mb-4">Shopping Cart</h4>

    @if ($cart && count($cart)>0)

    <div class="row g-4">

        {{-- LEFT: ITEMS --}}
        <div class="col-md-8">

            <div class="border rounded-3 bg-white">

                @php $grandTotal = 0; @endphp

                @foreach ($cart as $productId => $item)

                    @php
                        $total = $item['price'] * $item['quantity'];
                        $grandTotal += $total;
                    @endphp

                    <div class="d-flex align-items-center p-3 border-bottom">

                        {{-- IMAGE --}}
                        <img src="{{ asset('storage/'.$item['image']) }}"
                             width="70" height="70"
                             class="rounded"
                             style="object-fit: cover;">

                        {{-- DETAILS --}}
                        <div class="ms-3 flex-grow-1">

                            <div class="fw-medium">
                                {{ $item['name'] }}
                            </div>

                            <small class="text-muted">
                                ₹{{ number_format($item['price'],2) }}
                            </small>

                            {{-- QTY --}}
                            <div class="d-flex align-items-center mt-2 gap-2" data-id="{{ $productId }}">

                                <button class="btn btn-sm btn-light border btn-dec"
                                        data-id="{{$productId}}">
                                    −
                                </button>

                                <span class="qty">{{ $item['quantity'] }}</span>

                                <button class="btn btn-sm btn-light border btn-inc"
                                        data-id="{{ $productId }}">
                                    +
                                </button>

                                <button class="btn btn-sm text-muted btn-remove"
                                        data-id="{{ $productId }}">
                                    Remove
                                </button>

                            </div>

                        </div>

                        {{-- PRICE --}}
                        <div class="fw-medium item-total">
                            ₹{{ number_format($total,2) }}
                        </div>

                    </div>

                @endforeach

            </div>

        </div>

        {{-- RIGHT: SUMMARY --}}
        <div class="col-md-4">

            <div class="border rounded-3 p-4 bg-white">

                <h5 class="fw-semibold mb-3">Order Summary</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span id="grand-total">₹{{ number_format($grandTotal,2) }}</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-4 fw-semibold">
                    <span>Total</span>
                    <span>₹{{ number_format($grandTotal,2) }}</span>
                </div>

            <form action="{{ route('checkout') }}" method="POST">
                @csrf
                <button class="btn btn-dark w-100">
                    Checkout
                </button>
            </form>

                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-secondary w-100 mb-2">
                    Continue Shopping
                </a>

                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn w-100 text-muted border">
                        Clear Cart
                    </button>
                </form>

            </div>

        </div>

    </div>

    @else

    <div class="text-center py-5">
        <h5 class="fw-semibold mb-2">Your cart is empty</h5>
        <p class="text-muted small mb-3">
            Start adding products to continue
        </p>

        <a href="{{ route('products.index') }}" class="btn btn-dark">
            Browse Products
        </a>
    </div>

    @endif

</div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.btn-inc', function () {
        let id = $(this).data('id');

        $.post(`/cart/increment/${id}`, function () {
            location.reload();
        });
    });

    $(document).on('click', '.btn-dec', function () {
        let id = $(this).data('id');

        $.post(`/cart/decrement/${id}`, function () {
            location.reload();
        });
    });

    $(document).on('click', '.btn-remove', function () {
        let id = $(this).data('id');

        $.ajax({
            url: `/cart/remove/${id}`,
            type: 'DELETE',
            success: function () {
                location.reload();
            }
        });
    });

});
</script>
@endpush