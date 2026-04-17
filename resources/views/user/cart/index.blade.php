@extends('user.layouts.app')

@section('title', 'Cart')

@section('content')
<div class="container">

    <h2>Your Cart</h2>
    <p>Review items before checkout</p>

    <hr>

    <a href="{{ route('user.products.index') }}">Continue Shopping</a> |
    <a href="{{ route('user.checkout.index') }}">Checkout</a>

    <hr>

    @if (!empty($cart))

        <!-- Cart Items -->
        @foreach ($cart as $productId => $item)
            @php $lineTotal = $item['price'] * $item['quantity']; @endphp

            <div style="margin-bottom:15px; border-bottom:1px solid #ccc; padding-bottom:10px;">

                <p><strong>{{ $item['name'] }}</strong></p>
                <p>Price: ₹{{ number_format($item['price'], 2) }}</p>
                <p>Quantity: {{ $item['quantity'] }}</p>
                <p>Total: ₹{{ number_format($lineTotal, 2) }}</p>

                <!-- Actions -->
                <form method="POST" action="{{ route('user.cart.increment', $productId) }}" style="display:inline;">
                    @csrf
                    <button type="submit">+</button>
                </form>

                <form method="POST" action="{{ route('user.cart.decrement', $productId) }}" style="display:inline;">
                    @csrf
                    <button type="submit">-</button>
                </form>

                <form method="POST" action="{{ route('user.cart.remove', $productId) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Remove</button>
                </form>

            </div>
        @endforeach

        <hr>

        <!-- Summary -->
        <h3>Order Summary</h3>

        <p>Subtotal: ₹{{ number_format($summary['subtotal'] ?? 0, 2) }}</p>
        <p>Tax: ₹{{ number_format($summary['tax'] ?? 0, 2) }}</p>
        <p>Shipping: ₹{{ number_format($shipping['amount'] ?? 0, 2) }}</p>

        <h4>Total: ₹{{ number_format($grandTotal ?? 0, 2) }}</h4>

        <br>

        <a href="{{ route('user.checkout.index') }}">Proceed to Checkout</a>

        <form method="POST" action="{{ route('user.cart.clear') }}">
            @csrf
            @method('DELETE')
            <button type="submit">Clear Cart</button>
        </form>

    @else

        <p>Your cart is empty</p>
        <a href="{{ route('user.products.index') }}">Browse Products</a>

    @endif

</div>
@endsection