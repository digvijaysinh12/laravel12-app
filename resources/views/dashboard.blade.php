@extends('layouts.app')


@section('content')

<div class="container">

    <div class="row mb-4">
        <div class="col">
            <h3>Dashboard</h3>
            <p class="text-muted">Welcome, {{ $current_user->name ?? 'Guest' }}</p>
        </div>
    </div>
    

    <div class="row">

        {{-- Total Products --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Products</h6>
                    <h3>{{ \App\Models\Product::count() }}</h3>
                </div>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Cart Items</h6>
                    <h3>{{ count(session('cart', [])) }}</h3>
                </div>
            </div>
        </div>

        {{-- App Name --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Application</h6>
                    <h5>{{ $app_name }}</h5>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection