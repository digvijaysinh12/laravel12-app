<div class="card shadow-sm h-100 border-0">

    {{-- Product Image --}}
    @if ($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height:220px; object-fit:cover;">
    @else
        <img src="https://via.placeholder.com/300x220?text=No+Image" class="card-img-top"
            style="height:220px; object-fit:cover;">
    @endif

    <div class="card-body d-flex flex-column">

        <h6 class="fw-semibold mb-1">
            {{ $product->name }}
        </h6>

        <small class="text-muted mb-2">
            {{ $product->category->name ?? 'Uncategorized' }}
        </small>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold">
                @currency($product->price)
            </span>

            @if ($product->price > 1000)
                <span class="badge bg-success">Premium</span>
            @else
                <span class="badge bg-light text-dark">Standard</span>
            @endif
        </div>

        <div class="mt-auto">

            <div class="d-flex gap-2 mb-2">
                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-dark w-100">
                    View
                </a>
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary w-100">
                        Edit
                    </a>
                @endif

            </div>
            
            @if(auth()->check() && auth()->user()->role === 'user')
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-dark w-100">Add to Cart</button>
                </form>
            @endif


        </div>

    </div>
</div>