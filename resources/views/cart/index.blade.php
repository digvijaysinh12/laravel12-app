@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h4 class="mb-4 fw-bold">🛒 Shopping Cart</h4>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($cart && $cart->items->count())

    <div class="row">

        {{-- LEFT: CART ITEMS --}}
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-body p-0">

                    @php $grandTotal = 0; @endphp

                    @foreach ($cart->items as $item)

                        @php
                            $total = $item->product->price * $item->quantity;
                            $grandTotal += $total;
                        @endphp

                        <div class="d-flex align-items-center border-bottom p-3">

                            {{-- IMAGE --}}
                            <img src="{{ asset('storage/'.$item->product->image) }}"
                                 width="90" height="90"
                                 class="rounded me-3"
                                 style="object-fit: cover; border:1px solid #eee;">

                            {{-- DETAILS --}}
                            <div class="flex-grow-1">

                                <h6 class="mb-1 fw-semibold">
                                    {{ $item->product->name }}
                                </h6>

                                <small class="text-muted">
                                    ₹{{ number_format($item->product->price,2) }}
                                </small>

                                {{-- QUANTITY CONTROLS --}}
                                <div class="d-flex align-items-center mt-2">

                                    {{-- DECREMENT --}}
                                    <button class="btn btn-sm btn-outline-secondary btn-dec"
                                            data-id="{{ $item->product_id }}">
                                        −
                                    </button>

                                    {{-- QTY --}}
                                    <span class="mx-2 fw-bold">
                                        {{ $item->quantity }}
                                    </span>

                                    {{-- INCREMENT --}}
                                    <button class="btn btn-sm btn-outline-secondary btn-inc"
                                            data-id="{{ $item->product_id }}">
                                        +
                                    </button>

<button class="btn btn-sm btn-outline-danger btn-remove"
        data-id="{{ $item->product_id }}">
    🗑
</button>

                                </div>

                            </div>

                            {{-- ITEM TOTAL --}}
                            <div class="text-end">
                                <strong class="text-dark">
                                    ₹{{ number_format($total,2) }}
                                </strong>
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

                    <h5 class="mb-3 fw-semibold">Order Summary</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>₹{{ number_format($grandTotal,2) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total</span>
                        <strong class="fw-bold">
                            ₹{{ number_format($grandTotal,2) }}
                        </strong>
                    </div>

                    {{-- CHECKOUT --}}
                    <a href="#" class="btn btn-dark w-100 mb-2">
                        Proceed to Checkout
                    </a>

                    {{-- CONTINUE SHOPPING --}}
                    <a href="{{ route('products.index') }}"
                       class="btn btn-outline-secondary w-100 mb-2">
                        Continue Shopping
                    </a>

                    {{-- CLEAR CART --}}
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

    {{-- EMPTY CART --}}
    <div class="text-center py-5">
        <h5 class="mb-3">🛒 Your cart is empty</h5>
        <p class="text-muted">Looks like you haven't added anything yet</p>

        <a href="{{ route('products.index') }}" class="btn btn-dark mt-2">
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