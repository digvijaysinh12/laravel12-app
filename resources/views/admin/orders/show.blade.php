@extends('layouts.app')

@section('content')

<h2>Order Details</h2>

<p><strong>Order No:</strong> {{ $order->order_number }}</p>
<p><strong>User:</strong> {{ $order->user->name }}</p>
<p><strong>Total:</strong> ₹{{ $order->total_amount }}</p>

---

<h3>Update Status</h3>

<form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
    @csrf
    @method('PUT')

    <select name="status">
        @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $status)
            <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                {{ ucfirst($status) }}
            </option>
        @endforeach
    </select>

    <button type="submit">Update</button>
</form>

---

<h3>Items</h3>

<table border="1" width="100%" cellpadding="10">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Total</th>
    </tr>

    @foreach($order->items as $item)
    <tr>
        <td>{{ $item->product->name ?? 'Deleted' }}</td>
        <td>₹{{ $item->price }}</td>
        <td>{{ $item->quantity }}</td>
        <td>₹{{ $item->price * $item->quantity }}</td>
    </tr>
    @endforeach

</table>

@endsection