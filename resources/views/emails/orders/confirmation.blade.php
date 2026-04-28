<x-mail::message>
@php
    $logoPath = public_path('images/logo.png');
@endphp

@if(file_exists($logoPath))
    <img src="{{ $message->embed($logoPath) }}" width="150">
@endif
# Order Confirmation

Thank you for your order!

<x-mail::table>
| Product | Qty | Price |
|--------|-----|-------|
@foreach($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | ₹{{ $item->price }} |
@endforeach
</x-mail::table>

**Total: ₹{{ $order->total }}**

<x-mail::button :url="$url">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
