@extends('user.layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-12">

    {{-- Page Header --}}
    <div class="mb-10 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">

        <div>

            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                Shopping Cart
            </p>

            <h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">
                Your Cart
            </h1>

            <p class="mt-3 text-sm text-slate-500">
                {{ $summary['item_count'] }} items added to your cart
            </p>

        </div>

        <a href="{{ route('user.products.index') }}"
           class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-medium text-slate-700 transition hover:bg-slate-900 hover:text-white">

            Continue Shopping

        </a>

    </div>

    @if(!empty($cart))

    <div class="grid gap-8 lg:grid-cols-3">

        {{-- Cart Items --}}
        <div class="space-y-5 lg:col-span-2">

            @foreach($cart as $productId => $item)

                @php
                    $lineTotal = $item['price'] * $item['quantity'];
                @endphp

<article id="cart-item-{{ $productId }}"
         class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:shadow-lg">
                    <div class="flex flex-col gap-6 md:flex-row">

                        {{-- Product Image --}}
                        <div class="h-32 w-32 shrink-0 overflow-hidden rounded-2xl bg-slate-100">

                            <img src="{{ $item['image'] ?? 'https://placehold.co/500x500' }}"
                                 class="h-full w-full object-cover">

                        </div>

                        {{-- Product Details --}}
                        <div class="flex flex-1 flex-col justify-between">

                            <div class="flex items-start justify-between gap-5">

                                <div>

                                    <p class="text-xs uppercase tracking-[0.16em] text-slate-400">
                                        Product
                                    </p>

                                    <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">
                                        {{ $item['name'] }}
                                    </h2>

                                    <p class="mt-3 text-sm text-slate-500">
                                        Premium quality item with modern design.
                                    </p>

                                    <p class="mt-5 text-lg font-semibold text-slate-900">
                                        ₹{{ number_format($item['price'], 2) }}
                                    </p>

                                </div>

                                {{-- Remove --}}
                                <form method="POST"
                                    action="{{ route('user.cart.remove', $productId) }}"
                                    class="remove-form"
                                    data-id="{{ $productId }}">

                                    @csrf
                                    @method('DELETE')

                                    <button class="text-sm font-medium text-slate-400 transition hover:text-red-500">
                                        Remove
                                    </button>

                                </form>

                            </div>

                            {{-- Footer --}}
                            <div class="mt-6 flex items-center justify-between">

                                {{-- Quantity --}}
                                <div class="flex items-center overflow-hidden rounded-xl border border-slate-300 bg-white">

                                    {{-- Decrement --}}
                                    <form method="POST"
                                        action="{{ route('user.cart.decrement', $productId) }}"
                                        class="decrement-form"
                                        data-id="{{ $productId }}">

                                        @csrf

                                        <button type="submit"
                                            class="flex h-11 w-11 items-center justify-center text-lg text-slate-700 transition hover:bg-slate-100">

                                            −

                                        </button>

                                    </form>

                                    {{-- Quantity --}}
                                    <div id="quantity-{{ $productId }}"
                                        class="flex h-11 w-12 items-center justify-center border-x border-slate-300 text-sm font-medium text-slate-900">

                                        {{ $item['quantity'] }}

                                    </div>

                                    {{-- Increment --}}
                                    <form method="POST"
                                        action="{{ route('user.cart.increment', $productId) }}"
                                        class="increment-form"
                                        data-id="{{ $productId }}">

                                        @csrf

                                        <button type="submit"
                                            class="flex h-11 w-11 items-center justify-center text-lg text-slate-700 transition hover:bg-slate-100">

                                            +

                                        </button>

                                    </form>

                                </div>

                                {{-- Total --}}
                                <div class="text-right">

                                    <p class="text-xs uppercase tracking-[0.16em] text-slate-400">
                                        Total
                                    </p>

                                    <p id="total-{{ $productId }}"
                                    class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">

                                        ₹{{ number_format($lineTotal, 2) }}

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

        {{-- Summary --}}
        <aside>

            <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                        Summary
                    </p>

                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
                        Order Summary
                    </h2>

                </div>

                {{-- Summary Items --}}
                <div class="mt-8 space-y-5">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Subtotal
                        </span>

                        <span id="subtotal" class="text-sm font-medium text-slate-900">
                                ₹{{ number_format($summary['subtotal'], 2) }}
                        </span>

                    </div>

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Tax
                        </span>

                        <span id="tax" class="text-sm font-medium text-slate-900">
                            ₹{{ number_format($summary['tax'], 2) }}
                        </span>

                    </div>

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Shipping
                        </span>

                        <span id="shipping" class="text-sm font-medium text-slate-900">
                                ₹{{ number_format($shipping['amount'], 2) }}
                        </span>

                    </div>

                </div>

                <div class="my-7 border-t border-slate-200"></div>

                {{-- Grand Total --}}
                <div class="flex items-center justify-between">

                    <span class="text-base font-medium text-slate-900">
                        Total
                    </span>

                    <span id="grand-total"
                          class="text-3xl font-semibold tracking-tight text-slate-900">
                        ₹{{ number_format($grandTotal, 2) }}
                    </span>

                </div>

                {{-- Checkout --}}
                <a href="{{ route('user.checkout.index') }}"
                   class="mt-7 flex h-12 w-full items-center justify-center rounded-xl bg-slate-900 text-sm font-medium text-white transition hover:bg-black">

                    Proceed to Checkout

                </a>

                {{-- Clear Cart --}}
                <form method="POST"
                    action="{{ route('user.cart.clear') }}"
                    class="mt-3 clear-cart-form">

                    @csrf
                    @method('DELETE')

                    <button class="h-12 w-full rounded-xl border border-slate-300 text-sm font-medium text-slate-700 transition hover:bg-slate-100">

                        Clear Cart

                    </button>

                </form>

                {{-- Note --}}
                <p class="mt-5 text-center text-xs leading-relaxed text-slate-400">
                    Secure checkout with encrypted payment and order protection.
                </p>

            </div>

        </aside>

    </div>

    @else

    {{-- Empty State --}}
    <div class="rounded-3xl border border-dashed border-slate-300 bg-white py-28 text-center shadow-sm">

        <h2 class="text-3xl font-semibold tracking-tight text-slate-900">
            Your cart is empty
        </h2>

        <p class="mt-3 text-sm text-slate-500">
            Browse products and add items to continue shopping.
        </p>

        <a href="{{ route('user.products.index') }}"
           class="mt-8 inline-flex h-12 items-center justify-center rounded-xl bg-slate-900 px-6 text-sm font-medium text-white transition hover:bg-black">

            Browse Products

        </a>

    </div>

    @endif

</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

$(document).ready(function () {

    // Increment
    $('.increment-form').submit(function (e) {

        e.preventDefault();

        let form = $(this);
        let productId = form.data('id');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },

            success: function (data) {

                $('#quantity-' + productId).text(data.quantity);

                $('#total-' + productId).text(
                    '₹' + Number(data.itemTotal).toFixed(2)
                );

                updateSummary(data);
            }
        });

    });

    // Decrement
    $('.decrement-form').submit(function (e) {

        e.preventDefault();

        let form = $(this);
        let productId = form.data('id');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },

            success: function (data) {

                $('#quantity-' + productId).text(data.quantity);

                $('#total-' + productId).text(
                    '₹' + Number(data.itemTotal).toFixed(2)
                );

                updateSummary(data);
            }
        });

    });

    // Summary Update
    function updateSummary(data)
    {
        $('#subtotal').text(
            '₹' + Number(data.subtotal).toFixed(2)
        );

        $('#tax').text(
            '₹' + Number(data.tax).toFixed(2)
        );

        $('#shipping').text(
            '₹' + Number(data.shipping).toFixed(2)
        );

        $('#grand-total').text(
            '₹' + Number(data.grandTotal).toFixed(2)
        );
    }


     // Remove Item
$('.remove-form').submit(function (e) {

    e.preventDefault();

    let form = $(this);
    let productId = form.data('id');

    $.ajax({

        url: form.attr('action'),
        type: 'POST',

        data: {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        },

        success: function (data) {

            $('#cart-item-' + productId).remove();

            updateSummary(data);

        }

    });

});


// Clear Cart
$('.clear-cart-form').submit(function (e) {

    e.preventDefault();

    let form = $(this);

    $.ajax({

        url: form.attr('action'),
        type: 'POST',

        data: {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        },

        success: function () {

            location.reload();

        }

    });

});

});

</script>
@endsection
