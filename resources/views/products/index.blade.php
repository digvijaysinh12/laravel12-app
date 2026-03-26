@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        <!-- Header + Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <!-- Title -->
            <div>
                <h4 class="mb-0">{{ $page_title }}</h4>
                <small class="text-muted">
                    Total Products: {{ $total_products }}
                </small>
            </div>

            <!-- Search -->
            <form method="GET" action="{{ route('products.index') }}" class="d-flex">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2"
                    placeholder="Search products...">
                <button class="btn btn-outline-dark">Search</button>
            </form>

        @if(auth()->check() && auth()->user()->role === 'admin')
        <div class="d-flex gap-2">

            <a href="{{ route('products.export') }}" class="btn btn-success">
                ⬇️ Download CSV
            </a>

            <a href="{{ route('products.create') }}" class="btn btn-primary">
                ➕ Add Product
            </a>

        </div>
        @endif

        </div>

        <!-- Product Grid -->
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

        <!-- Page Info -->
        <div class="text-center text-muted mt-4">
            Showing {{ $products->firstItem() ?? 0 }}
            to {{ $products->lastItem() ?? 0 }}
            of {{ $products->total() }} results
        </div>

        <!-- Pagination Buttons -->
        <div class="d-flex justify-content-center mt-2">
            {{ $products->links() }}
        </div>

    </div>

@endsection