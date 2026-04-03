@extends('layouts.app')

@section('content')
    <div class="row g-4">
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card__body p-0">
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid w-100 product-hero" alt="{{ $product->name }}">
                </div>
            </div>
        </div>

        <div class="col-md-7">
            @php $stock = $product->stock ?? 0; @endphp
            <x-card :title="$product->name">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="text-muted">Rs. {{ number_format($product->price) }}</span>
                    <span class="badge {{ $stock > 10 ? 'bg-success' : ($stock > 0 ? 'bg-warning text-dark' : 'bg-danger') }} product-stock-badge"
                        data-product-id="{{ $product->id }}"
                        data-stock="{{ $stock }}">
                        {{ $stock > 0 ? "Stock: $stock" : 'Out of Stock' }}
                    </span>
                </div>
                <p class="mb-2"><strong>Category:</strong> {{ $product->category->name }}</p>
                <p class="text-muted">{{ $product->description }}</p>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <x-button variant="secondary" href="{{ route('products.index') }}">Back</x-button>

                    @if(auth()->check() && auth()->user()->role === 'user')
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <x-button type="submit" class="btn-sm add-to-cart-btn"
                                data-product-id="{{ $product->id }}"
                                @disabled($stock <= 0)>
                                {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                            </x-button>
                        </form>
                    @endif

                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <x-button variant="secondary" href="{{ route('admin.products.edit', $product->id) }}" class="btn-sm">Edit</x-button>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

@endsection
