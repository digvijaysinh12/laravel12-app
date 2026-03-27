@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        <div class="row">

            <div class="col-md-5">
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" style="border-radius: 8px;">
            </div>

            <div class="col-md-7">

                <h4>{{ $product->name }}</h4>

                <h5 class="text-muted mb-3">₹{{ number_format($product->price) }}</h5>

                <p class="mb-2"><strong>Category:</strong> {{ $product->category->name }}</p>

                <p class="text-muted">{{ $product->description }}</p>

                <div class="mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back</a>
                    @if(auth()->check() && auth()->user()->role === 'user')
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">Add to Cart</button>
                        </form>
                    @endif

                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary w-100">
                            Edit
                        </a>
                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection