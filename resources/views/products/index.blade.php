@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $page_title }}</h4>
        <p class="text-muted">Total Products: {{ $total_products }}</p>
        <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($products as $p)
                        <tr>
                            <td>{{ $p->name }}</td>

                            <td>₹{{ number_format($p->price) }}</td>

                            <td>{{ $p->category }}</td>

                            <td>
                                <img src="{{ asset('storage/'.$p->image) }}"
                                     width="60" height="60"
                                     style="object-fit: cover; border-radius: 6px;">
                            </td>

                            <td class="text-end">
                                <a href="{{ route('products.show', $p->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>

                                <form action="{{ route('products.destroy', $p->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this product?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No products available
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection