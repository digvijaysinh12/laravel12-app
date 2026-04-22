<h1 class="text-2xl font-bold mb-4">Products</h1>

<div class="grid grid-cols-4 gap-6">
    @forelse ($products as $product)
        <div class="border rounded-lg p-4 shadow">

            <img src="{{ $product['image'] }}"
                 alt="{{ $product['title'] }}"
                 class="h-40 w-full object-contain mb-3">

            <h2 class="font-semibold text-lg">
                {{ $product['title'] }}
            </h2>

            <p class="text-green-600 font-bold">
                ₹ {{ $product['price'] }}
            </p>

        </div>
    @empty
        <p>No products found</p>
    @endforelse
</div>