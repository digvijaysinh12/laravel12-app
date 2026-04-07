@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
    <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover" alt="{{ $product->name }}">
    </div>

    <div class="space-y-5">
        @php $stock = $product->stock ?? 0; @endphp
        <x-card :title="$product->name">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-2xl font-semibold text-slate-900">Rs. {{ number_format($product->price, 2) }}</span>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $stock > 10 ? 'bg-emerald-100 text-emerald-700' : ($stock > 0 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                    {{ $stock > 0 ? "Stock: $stock" : 'Out of Stock' }}
                </span>
            </div>

            <div class="mt-5 space-y-3 text-sm text-slate-600">
                <p><span class="font-medium text-slate-900">Category:</span> {{ $product->category->name ?? 'Uncategorized' }}</p>
                <p>{{ $product->description }}</p>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <x-button variant="secondary" href="{{ route('user.products.index') }}">Back</x-button>

                @if(auth()->check() && auth()->user()->role === 'user')
                    <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                        @csrf
                        <x-button type="submit" class="add-to-cart-btn" @disabled($stock <= 0)>
                            {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                        </x-button>
                    </form>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <x-button variant="secondary" href="{{ route('admin.products.edit', $product->id) }}">Edit</x-button>
                @endif
            </div>
        </x-card>
    </div>
</div>
@endsection
