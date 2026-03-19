@extends('layouts.app')

@section('content')

<div class="container mt-4">
    
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $page_title }}</h4>
    <p class="text-muted mb-0">Total Products: {{ $total_products }}</p>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
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

</div>

@endsection
