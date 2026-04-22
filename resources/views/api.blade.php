@foreach($products as $product)
    <div>
        <h3>{{ $product['title'] }}</h3>
        <p>{{ $product['price'] }}</p>
        <img src="{{ $product['image'] }}" width="100">
    </div>
@endforeach
