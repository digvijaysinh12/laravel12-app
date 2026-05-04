<x-mail::message>
@php
    $logoPath = public_path('images/logo.png');
@endphp

@if (file_exists($logoPath))
<p>
    <img src="{{ $message->embed($logoPath) }}" alt="{{ config('app.name') }}" width="140">
</p>
@endif

# {{ __('emails.order.heading') }}

{{ __('emails.order.greeting', ['name' => $order->user->name]) }}

{{ __('emails.order.intro', ['id' => $order->order_number]) }}

<x-mail::table>
| {{ __('emails.order.table.product') }} | {{ __('emails.order.table.quantity') }} | {{ __('emails.order.table.price') }} | {{ __('emails.order.table.total') }} |
| :-- | :--: | --: | --: |
@foreach ($order->items as $item)
| {{ $item->product->name ?? __('emails.order.product_fallback') }} | {{ $item->quantity }} | Rs. {{ number_format($item->price, 2) }} | Rs. {{ number_format($item->price * $item->quantity, 2) }} |
@endforeach
</x-mail::table>

**{{ __('emails.order.total') }}: Rs. {{ number_format($order->total_amount, 2) }}**

<x-mail::button :url="$url">
{{ __('emails.order.view_order') }}
</x-mail::button>

{{ __('emails.order.attachment_notice') }}

{{ __('emails.order.thanks') }},<br>
{{ config('app.name') }}
</x-mail::message>
