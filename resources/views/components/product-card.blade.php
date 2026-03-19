<div class="card shadow-sm h-100">

@if ($product->image)
<img src="{{ asset('storage/' . $product->image) }}"
     class="card-img-top"
     style="height:200px; object-fit:cover;">
@else
<img src="{{ asset('storage/' . $product->image) }}"
     class="card-img-top"
     style="height:200px; object-fit:cover;">
@endif

<div class="card-body">
    <h5 class="card-title">{{ $product->name }}</h5>

    <p class="card-text mb-1">
        <strong>Price:</strong> ₹{{ number_format($product->price) }}
    </p>
    @if ($product->price>1000)
        <span class="badge bg-success">Premium</span> 
    @else
        <span class="badge bg-secondary">Normal</span>   
    @endif

    
    <p class="card-text mb-2">
        <strong>Category:</strong> {{ $product->category ?? 'No Category'}}
    </p>

    <div class="d-flex justify-content-between">
        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    </div>
</div>

</div>
