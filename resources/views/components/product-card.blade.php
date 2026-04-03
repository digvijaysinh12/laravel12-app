<div class="card shadow-sm h-100 border-0" data-product-id="{{ $product->id }}" data-stock="{{ $stock }}">

    {{-- Product Image --}}
    @if ($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-thumb" alt="{{ $product->name }}">
    @else
        <img src="https://via.placeholder.com/300x220?text=No+Image" class="card-img-top product-thumb" alt="No image">
    @endif

    <div class="card-body d-flex flex-column">

        <h6 class="fw-semibold mb-1">
            {{ $product->name }}
        </h6>

        <small class="text-muted mb-2">
            {{ $product->category->name ?? 'Uncategorized' }}
        </small>
        @php
            $stock = $product->stock ?? 0;
        @endphp

        <div class="mb-2">
            @if($stock > 10)
                <span class="badge bg-success">
                    In Stock ({{ $stock }})
                </span>
            @elseif($stock > 0)
                <span class="badge bg-warning text-dark">
                    Low Stock ({{ $stock }})
                </span>
            @else
                <span class="badge bg-danger">
                    Out of Stock
                </span>
            @endif
        </div>


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
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary w-100">
                        Edit
                    </a>
                @endif

            </div>

            @if(auth()->check() && auth()->user()->role === 'user')
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-dark w-100 add-to-cart-btn"
                        @disabled($stock <= 0)
                        data-product-id="{{ $product->id }}">
                        {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                    </button>
                </form>
            @endif


        </div>

    </div>
</div>
