<x-mail::message>
# {{ __('emails.admin.low_stock_heading') }}

{{ __('emails.admin.low_stock_intro', ['threshold' => $threshold]) }}

<x-mail::table>
| {{ __('emails.admin.table.product') }} | {{ __('emails.admin.table.stock') }} |
| :-- | --: |
@foreach($products as $product)
| {{ $product->name }} | {{ $product->stock }} |
@endforeach
</x-mail::table>

{{ __('emails.admin.low_stock_footer') }}

{{ __('emails.order.thanks') }},<br>
{{ config('app.name') }}
</x-mail::message>
