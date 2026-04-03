@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $page_title }}</h4>
            <small class="text-muted">Total Products: {{ $total_products }}</small>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
            <form method="GET" action="{{ route('products.index') }}" class="d-flex">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search products...">
                <button class="btn btn-outline-dark">Search</button>
            </form>

            @if(auth()->check() && auth()->user()->role === 'admin')
                <x-button href="{{ route('products.export') }}" variant="secondary">Download CSV</x-button>
                <x-button href="{{ route('admin.products.create') }}">Add Product</x-button>
            @endif
        </div>
    </div>

    <div class="row">
        @forelse ($products as $p)
            <div class="col-md-4 mb-3">
                <x-product-card :product="$p" />
            </div>
        @empty
            <div class="col-12 text-center text-muted py-4">
                No products available
            </div>
        @endforelse
    </div>

    <div class="text-center text-muted mt-4">
        Showing {{ $products->firstItem() ?? 0 }}
        to {{ $products->lastItem() ?? 0 }}
        of {{ $products->total() }} results
    </div>

    <div class="d-flex justify-content-center mt-2">
        {{ $products->links() }}
    </div>
@endsection
