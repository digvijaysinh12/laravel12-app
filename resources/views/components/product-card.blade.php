<div class="flex h-full flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    @if ($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" class="h-52 w-full object-cover" alt="{{ $product->name }}">
    @else
        <div class="flex h-52 items-center justify-center bg-slate-100 text-sm text-slate-400">
            No Image
        </div>
    @endif

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-semibold tracking-tight text-slate-900">{{ $product->name }}</h3>
        <p class="mt-1 text-sm text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>

        @php $stock = $product->stock ?? 0; @endphp

        <div class="mt-4">
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $stock > 10 ? 'bg-emerald-100 text-emerald-700' : ($stock > 0 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                {{ $stock > 10 ? 'In stock' : ($stock > 0 ? 'Low stock' : 'Out of stock') }}
            </span>
        </div>

        <div class="mt-4 text-xl font-semibold text-slate-900">
            @currency($product->price)
        </div>

        <div class="mt-5 space-y-2">
            <a href="{{ route('user.products.show', $product->id) }}"
               class="block rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                View
            </a>

            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.products.edit', $product->id) }}"
                   class="block rounded-2xl bg-slate-950 px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-slate-800">
                    Edit
                </a>
            @endif

            @if(auth()->check() && auth()->user()->role === 'user')
                <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-300"
                        @disabled($stock <= 0)>
                        {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
