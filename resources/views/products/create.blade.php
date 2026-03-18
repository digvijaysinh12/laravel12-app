@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="card shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-body">

            <h4 class="mb-4 text-center fw-bold">➕ Create Product</h4>

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter product name">
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" placeholder="Enter price">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Enter description"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="Enter category">
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">⬅ Back</a>

                    <button type="submit" class="btn btn-primary">
                        💾 Save Product
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection