@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="card shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-body">

            <h4 class="mb-4 text-center fw-bold">✏ Edit Product</h4>

            <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $product->name }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control"
                           value="{{ $product->price }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ $product->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control"
                           value="{{ $product->category }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <img src="{{ asset('storage/'.$product->image) }}" 
                         width="100" class="rounded shadow-sm mb-2">
                </div>

                <div class="mb-3">
                    <label class="form-label">Change Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        ⬅ Back
                    </a>

                    <button type="submit" class="btn btn-primary">
                        💾 Update
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection