@extends('layouts.app')

@section('title', 'Out of Stock')

@section('content')
<div class="mx-auto max-w-2xl rounded-[2rem] border border-dashed border-rose-300 bg-rose-50 px-6 py-16 text-center shadow-sm">
    <p class="text-xs uppercase tracking-[0.24em] text-rose-700">Inventory</p>
    <h1 class="mt-3 text-3xl font-semibold text-slate-900">Product is out of stock</h1>
    <p class="mt-3 text-sm text-slate-600">This item is currently unavailable. Please check back later or browse other products.</p>
    <div class="mt-6">
        <x-button href="{{ route('user.products.index') }}">Browse Products</x-button>
    </div>
</div>
@endsection
