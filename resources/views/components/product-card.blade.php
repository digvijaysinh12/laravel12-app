<div class="flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
    @if ($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" class="h-48 w-full object-cover" alt="{{ $product->name }}">
    @else
        <div class="flex h-48 items-center justify-center bg-slate-100 text-sm text-slate-500">
            No Image
        </div>
    @endif

    <div class="flex flex-1 flex-col p-4">
        <h3 class="text-base font-semibold text-slate-900">{{ $product->name }}</h3>
        <p class="mt-1 text-sm text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>

        @php $stock = $product->stock ?? 0; @endphp

        <div class="mt-3">
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $stock > 10 ? 'bg-emerald-100 text-emerald-700' : ($stock > 0 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                {{ $stock > 10 ? 'In stock' : ($stock > 0 ? 'Low stock' : 'Out of stock') }}
            </span>
        </div>

        <div class="mt-3 text-lg font-semibold text-slate-900">
            INR {{ number_format($product->price, 2) }}
        </div>

        <div class="mt-4 space-y-2">
            <a href="{{ route('user.products.show', $product->id) }}"
               class="block rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                View Details
            </a>

            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.products.edit', $product->id) }}"
                   class="block rounded-md bg-slate-900 px-4 py-2 text-center text-sm font-medium text-white transition hover:bg-black">
                    Edit
                </a>
            @endif

            @if(auth()->check() && auth()->user()->role === 'user')
                <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black disabled:cursor-not-allowed disabled:bg-slate-300"
                        @disabled($stock <= 0)>
                        {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
