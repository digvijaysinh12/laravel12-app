@extends('admin.layouts.app')

@section('page-title', 'Products')

@section('content')
<div class="space-y-5">

    <!-- 🔹 Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-slate-500">Total Products</p>
            <p class="text-xl font-semibold mt-1">{{ $products->total() }}</p>
        </div>

        <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-slate-500">Low Stock</p>
            <p class="text-xl font-semibold text-amber-600 mt-1">
                {{ $products->where('stock','<',10)->count() }}
            </p>
        </div>

        <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-slate-500">Out of Stock</p>
            <p class="text-xl font-semibold text-rose-600 mt-1">
                {{ $products->where('stock',0)->count() }}
            </p>
        </div>
    </div>

    <!-- 🔹 Filters -->
    <div class="rounded-xl border bg-white p-4">
        <form id="filter-form" class="grid gap-3 md:grid-cols-4">

            <input type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Search product..."
                class="w-full rounded-lg border px-3 py-2 text-sm">

            <select name="category_id" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        @selected(request('category_id') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="sort" class="rounded-lg border px-3 py-2 text-sm">
                <option value="newest">Newest</option>
                <option value="price_asc">Price Low-High</option>
                <option value="price_desc">Price High-Low</option>
            </select>


        </form>
    </div>

    <!-- 🔹 Table -->
    <div id="product-table" class="rounded-xl border bg-white">

        <!-- Header -->
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <p class="text-sm text-slate-500">
                Showing {{ $products->firstItem() }}–{{ $products->lastItem() }}
            </p>

            <div class="flex gap-2">
            <button 
                onclick="exportProducts()"
                class="border px-3 py-1.5 rounded-lg text-sm">
                Export
            </button>

                <a href="{{ route('admin.products.create') }}"
                   class="bg-slate-900 text-white px-3 py-1.5 rounded-lg text-sm">
                    + Add
                </a>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-sm">

            <thead class="text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Price</th>
                    <th class="px-4 py-2 text-left">Stock</th>
                    <th class="px-4 py-2 text-left">Actions</th>                    
                    <th class="px-4 py-2 text-left">Size</th>

                </tr>
            </thead>

            <tbody>

                @forelse ($products as $product)
                    @php $stock = (int)$product->stock; @endphp

                    <tr id="row-{{ $product->id }}" class="border-b hover:bg-slate-50">

                        <!-- Product -->
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">
                                {{ $product->name }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </p>
                        </td>

                        <!-- Price -->
                        <td class="px-4 py-3">
                            ₹ {{ number_format($product->price, 2) }}
                        </td>

                        <!-- Stock -->
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded border
                                {{ $stock == 0 ? 'text-red-600' :
                                   ($stock < 10 ? 'text-amber-600' : 'text-green-600') }}">
                                {{ $stock == 0 ? 'Out' : $stock }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3">
                            <div class="flex gap-2">

                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="border px-3 py-1 rounded text-xs">
                                    Edit
                                </a>

                                <button 
                                    onclick="deleteProduct({{ $product->id }})"
                                    class="bg-red-600 text-white px-3 py-1 rounded text-xs">
                                    Delete
                                </button>

                            </div>
                        </td>

                        <td>
{{ $product->image_size }}  
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-slate-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $products->links() }}
        </div>

    </div>

</div>
@endsection


@push('scripts')
<script>

// 🔹 SAME STYLE AS YOUR BLADE TOAST
function showToast(message, tone = 'success') {

    let classes = {
        success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        danger: 'border-rose-200 bg-rose-50 text-rose-800',
        warning: 'border-amber-200 bg-amber-50 text-amber-800',
        default: 'border-slate-200 bg-white text-slate-700'
    };

    let toast = `
        <div class="rounded-xl border px-4 py-3 text-sm shadow-sm ${classes[tone]}">
            ${message}
        </div>
    `;

    $('#toast-container').append(toast);

    setTimeout(() => {
        $('#toast-container div').first().fadeOut(300, function () {
            $(this).remove();
        });
    }, 2500);
}


// 🔹 DELETE PRODUCT (AJAX)
function deleteProduct(id) {

    $.ajax({
        url: `/admin/products/${id}`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'DELETE'
        },

        success: function () {

            $('#row-' + id).fadeOut(200, function () {
                $(this).remove();
            });

            showToast('Product deleted successfully', 'success');
        },

        error: function () {
            showToast('Delete failed', 'danger');
        }
    });
}

// 🔥 FILTER AJAX
function loadProducts() {

    let formData = $('#filter-form').serialize();

    $.get("{{ route('admin.products.index') }}", formData, function (response) {

        // Replace only table section
        let html = $(response).find('#product-table').html();

        $('#product-table').html(html);
    });
}


// 🔹 Trigger on input change
$('#filter-form input, #filter-form select').on('change keyup', function () {
    loadProducts();
});

function exportProducts() {

    showToast('Preparing export...', 'warning');

    let params = $('#filter-form').serialize();

    $.ajax({
        url: "{{ route('admin.products.export') }}",
        type: "GET",
        data: params,

        xhrFields: {
            responseType: 'blob' // ✅ IMPORTANT
        },

        success: function (data, status, xhr) {

            // ✅ Get filename from header (optional)
            let disposition = xhr.getResponseHeader('Content-Disposition');
            let fileName = 'products.csv';

            if (disposition && disposition.indexOf('filename=') !== -1) {
                fileName = disposition.split('filename=')[1].replace(/"/g, '');
            }

            // ✅ Create download link
            let blob = new Blob([data], { type: 'text/csv' });
            let url = window.URL.createObjectURL(blob);

            let a = document.createElement('a');
            a.href = url;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();

            a.remove();
            window.URL.revokeObjectURL(url);

            showToast('Export downloaded successfully', 'success');
        },

        error: function (xhr) {

            // ✅ Try to read error message
            let reader = new FileReader();

            reader.onload = function () {
                try {
                    let res = JSON.parse(reader.result);
                    showToast(res.message || 'Export failed', 'danger');
                } catch {
                    showToast('Export failed', 'danger');
                }
            };

            reader.readAsText(xhr.response);
        }
    });
}
</script>
@endpush