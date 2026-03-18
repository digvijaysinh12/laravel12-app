@extends('layouts.app')

@section('content')

<div class="container mt-4" style="max-width: 600px;">

    <div class="card">
        <div class="card-body">

            <h5 class="mb-4">Create Product</h5>

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection