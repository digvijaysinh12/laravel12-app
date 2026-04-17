@extends('user.layouts.app')

@section('title', 'Order Details')

@section('content')
<div style="max-width:1000px; margin:auto;">

    <!-- Header -->
    <div style="background:#fff; padding:15px; border:1px solid #ddd; margin-bottom:15px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="margin:0;">Order #{{ $order->order_number }}</h2>
                <p style="margin:0; font-size:14px; color:gray;">
                    Placed on {{ $order->created_at->format('d M Y') }}
                </p>
            </div>

            <div>
                <span style="padding:5px 10px; background:#f0f0f0; font-size:13px;">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div style="display:flex; gap:15px;">

        <!-- LEFT: Items -->
        <div style="flex:2;">

            <div style="background:#fff; border:1px solid #ddd; padding:15px;">
                <h3>Items</h3>

                @foreach($order->items as $item)
                    <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding:10px 0;">

                        <div>
                            <p style="margin:0; font-weight:bold;">
                                {{ $item->product->name ?? 'Product' }}
                            </p>
                            <p style="margin:0; font-size:13px; color:gray;">
                                Qty: {{ $item->quantity }}
                            </p>
                        </div>

                        <div style="text-align:right;">
                            <p style="margin:0;">₹{{ number_format($item->price, 2) }}</p>
                            <p style="margin:0; font-weight:bold;">
                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                            </p>
                        </div>

                    </div>
                @endforeach
            </div>

        </div>

        <!-- RIGHT: Summary -->
        <div style="flex:1;">

            <!-- Price Summary -->
            <div style="background:#fff; border:1px solid #ddd; padding:15px; margin-bottom:15px;">
                <h3>Price Details</h3>

                <p>Total: ₹{{ number_format($order->total_amount, 2) }}</p>

                <hr>

                <h3 style="color:#2874f0;">
                    ₹{{ number_format($order->total_amount, 2) }}
                </h3>
            </div>

            <!-- Customer -->
            <div style="background:#fff; border:1px solid #ddd; padding:15px; margin-bottom:15px;">
                <h4>Customer</h4>
                <p>{{ $order->user->name }}</p>
                <p style="font-size:13px; color:gray;">{{ $order->user->email }}</p>
                <p style="font-size:13px;">{{ $order->phone }}</p>
            </div>

            <!-- Address -->
            <div style="background:#fff; border:1px solid #ddd; padding:15px; margin-bottom:15px;">
                <h4>Shipping Address</h4>
                <p style="font-size:14px;">{{ $order->shipping_address }}</p>
            </div>

            <!-- Actions -->
            <div style="background:#fff; border:1px solid #ddd; padding:15px; text-align:center;">

                <a href="{{ $signedUrl }}" 
                   style="display:block; padding:10px; background:#2874f0; color:#fff; text-decoration:none; margin-bottom:10px;">
                    Download Invoice
                </a>

                <p style="color:red; font-size:12px;">
                    Link expires in 10 minutes
                </p>

                <a href="{{ route('user.orders.index') }}" 
                   style="display:block; padding:8px; border:1px solid #ccc; text-decoration:none;">
                    Back to Orders
                </a>

            </div>

        </div>

    </div>

</div>
@endsection