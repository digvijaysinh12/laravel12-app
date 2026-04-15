@extends('admin.layouts.app')

@section('page-title', 'Categories')

@section('content')

<div class="space-y-5">

<!-- Top -->
<div class="flex justify-between items-center">
    <input type="text" id="searchInput"
           placeholder="Search category..."
           class="border px-3 py-2 rounded-lg text-sm w-60">

    <a href="{{ route('admin.categories.create') }}"
       class="bg-slate-900 text-white px-3 py-2 rounded-lg text-sm">
        + Add Category
    </a>
</div>

<!-- Table -->
<div class="rounded-xl border bg-white">
    <table class="w-full text-sm">

        <thead class="text-xs text-slate-500 border-b">
            <tr>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Products</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody id="categoryTable">

            @foreach ($categories as $category)
            <tr id="row-{{ $category->id }}" class="border-b hover:bg-slate-50">

                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 flex items-center justify-center rounded-md bg-slate-100">
                            {{ strtoupper(substr($category->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium">{{ $category->name }}</p>
                            <p class="text-xs text-slate-400">
                                {{ \Illuminate\Support\Str::slug($category->name) }}
                            </p>
                        </div>
                    </div>
                </td>

                <td class="px-4 py-3">
                    {{ $category->products_count }}
                </td>

                <td class="px-4 py-3 flex gap-2">

                    <a href="{{ route('admin.categories.edit', $category) }}"
                       class="border px-3 py-1 text-xs rounded">
                        Edit
                    </a>

                    <button onclick="deleteCategory({{ $category->id }})"
                            class="bg-red-600 text-white px-3 py-1 text-xs rounded">
                        Delete
                    </button>

                </td>
            </tr>
            @endforeach

        </tbody>

    </table>
</div>

</div>

@endsection

@push('scripts')

<script>

// 🔹 Search
$('#searchInput').on('keyup', function () {
    let value = $(this).val().toLowerCase();
    $('#categoryTable tr').filter(function () {
        $(this).toggle($(this).text().toLowerCase().includes(value));
    });
});

// 🔹 Delete AJAX
function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;

    $.ajax({
        url: `/admin/categories/${id}`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'DELETE'
        },
        success: function () {
            $('#row-' + id).remove();
            showToast('Category deleted', 'success');
        },
        error: function () {
            showToast('Delete failed', 'danger');
        }
    });
}

</script>

@endpush
