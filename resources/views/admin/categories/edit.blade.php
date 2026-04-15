@extends('admin.layouts.app')

@section('page-title', 'Edit Category')

@section('content')

<div class="space-y-5">

<!-- Header -->
<div class="flex justify-between">
    <h2 class="text-lg font-semibold">Edit Category</h2>
    <a href="{{ route('admin.categories.index') }}">← Back</a>
</div>

<!-- Form -->
<div class="bg-white p-5 rounded-xl border max-w-xl">

    <form id="editForm">

        @csrf
        @method('PUT')

        <input type="text" name="name" id="name"
               value="{{ $category->name }}"
               class="border w-full px-3 py-2 rounded">

        <p class="text-xs mt-1 text-slate-500">
            Slug:
            <span id="slugPreview">
                {{ \Illuminate\Support\Str::slug($category->name) }}
            </span>
        </p>

        <div class="mt-4 flex justify-end gap-2">
            <button type="submit"
                    class="bg-slate-900 text-white px-4 py-2 rounded">
                Update
            </button>
        </div>

    </form>

</div>

</div>

@endsection

@push('scripts')

<script>

// Slug preview
$('#name').on('input', function () {
    let slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    $('#slugPreview').text(slug || '-');
});

// Update AJAX
$('#editForm').on('submit', function (e) {

    e.preventDefault();

    $.ajax({
        url: "{{ route('admin.categories.update', $category) }}",
        type: "POST",
        data: $(this).serialize(),

        success: function () {
            showToast('Category updated', 'success');
            window.location.href = "{{ route('admin.categories.index') }}";
        },

        error: function () {
            showToast('Update failed', 'danger');
        }
    });

});

</script>

@endpush
