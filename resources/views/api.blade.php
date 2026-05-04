@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
        {{ $errors->first() }}
    </div>
@endif

@foreach($products as $product)
    <div>
        <h3>{{ $product['title'] }}</h3>
        <p>₹{{ $product['price'] }}</p>
        <img src="{{ $product['image'] }}" width="100">
    </div>
@endforeach
