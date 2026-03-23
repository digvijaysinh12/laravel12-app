@extends('layouts.app')

@section('content')

<h3 class="mb-3">Dashboard</h3>

<p class="text-muted">
    Welcome, {{ auth()->user()->name }}
</p>

<div class="row">

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-3">
            <h6>Total Products</h6>
            <h3>{{ \App\Models\Product::count() }}</h3>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-3">
            <h6>Cart Items</h6>
            <h3>{{ count(session('cart', [])) }}</h3>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-3">
            <h6>Application</h6>
            <h5>{{ config('app.name') }}</h5>
        </div>
    </div>

</div>

@endsection