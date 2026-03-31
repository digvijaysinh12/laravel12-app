@extends('layouts.app')

@section('content')

<h2>Orders</h2>

<table border="1" width="100%" cellpadding="10">
    <thead>
        <tr>
            <th>#</th>
            <th>Order No</th>
            <th>User</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->user->name }}</td>
            <td>₹{{ $order->total_amount }}</td>
            <td>
                <span style="color:
                    {{ $order->status == 'pending' ? 'orange' :
                       ($order->status == 'delivered' ? 'green' : 'blue') }}">
                    {{ ucfirst($order->status) }}
                </span>
            </td>
            <td>
                <a href="{{ route('admin.orders.show', $order->id) }}">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $orders->links() }}

@endsection