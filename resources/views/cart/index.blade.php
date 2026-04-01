@extends('layouts.app')

@section('content')
    <div class="container py-5" style="max-width: 1100px;">

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

                            <!-- ✅ FIXED: cart-row added -->
                            <div class="d-flex align-items-center p-3 border-bottom cart-row" data-id="{{ $productId }}">

                                <img src="{{ asset('storage/' . $item['image']) }}" width="70" height="70" class="rounded"
                                    style="object-fit: cover;">

                                <div class="ms-3 flex-grow-1">

                                    <div class="fw-medium">{{ $item['name'] }}</div>

                                    <small class="text-muted">
                                        ₹{{ number_format($item['price'], 2) }}
                                    </small>

                                    <div class="d-flex align-items-center mt-2 gap-2">

                                        <button class="btn btn-sm btn-light border btn-dec" data-id="{{ $productId }}">−</button>

                                        <span class="qty">{{ $item['quantity'] }}</span>

                                        <button class="btn btn-sm btn-light border btn-inc" data-id="{{ $productId }}">+</button>

                                        <button class="btn btn-sm text-muted btn-remove" data-id="{{ $productId }}">Remove</button>

                                    </div>
                                </div>

                                <div class="fw-medium item-total">
                                    ₹{{ number_format($total, 2) }}
                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded-3 p-4 bg-white">

                        <h5 class="fw-semibold mb-3">Order Summary</h5>

                        <!-- 🛒 ITEMS LIST -->
                        <div id="summary-items">
                            @foreach ($cart as $productId => $item)
                                <div class="d-flex justify-content-between small mb-2 summary-item" data-id="{{ $productId }}">
                                    <span>
                                        {{ $item['name'] }} × <span class="summary-qty">{{ $item['quantity'] }}</span>
                                    </span>
                                    <span class="summary-price">
                                        ₹{{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        <!-- TOTAL -->
                        <div class="d-flex justify-content-between mb-4 fw-semibold">
                            <span>Total</span>
                            <span id="final-total">₹{{ number_format($grandTotal, 2) }}</span>
                        </div>

                        <!-- ✅ KEEP BUTTONS -->
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button class="btn btn-dark w-100">Checkout</button>
                        </form>

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
                <h5>Your cart is empty</h5>
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

            function updateUI(row, res) {
                row.find('.qty').text(res.quantity);
                row.find('.item-total').text('₹' + res.itemTotal.toFixed(2));

                $('#grand-total').text('₹' + res.grandTotal.toFixed(2));
                $('#final-total').text('₹' + res.grandTotal.toFixed(2));
            }

            // INCREMENT
            $(document).on('click', '.btn-inc', function () {
                let id = $(this).data('id');
                let row = $(this).closest('.cart-row');

                $.post(`/cart/increment/${id}`, function (res) {
                    updateUI(row, res);
                });
            });

            // DECREMENT
            $(document).on('click', '.btn-dec', function () {
                let id = $(this).data('id');
                let row = $(this).closest('.cart-row');

                $.post(`/cart/decrement/${id}`, function (res) {

                    if (res.quantity <= 0) {
                        row.remove();
                    } else {
                        updateUI(row, res);
                    }

                    if (res.grandTotal <= 0) {
                        location.reload();
                    }
                });
            });

            // REMOVE
            $(document).on('click', '.btn-remove', function () {
                let id = $(this).data('id');
                let row = $(this).closest('.cart-row');

                $.ajax({
                    url: `/cart/remove/${id}`,
                    type: 'DELETE',
                    success: function (res) {

                        row.fadeOut(200, function () {
                            $(this).remove();
                        });

                        $('#grand-total').text('₹' + res.grandTotal.toFixed(2));
                        $('#final-total').text('₹' + res.grandTotal.toFixed(2));

                        if (res.grandTotal <= 0) {
                            location.reload();
                        }
                    }
                });
            });

        });
    </script>
@endpush