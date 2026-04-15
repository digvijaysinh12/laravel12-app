@extends('admin.layouts.app')

@section('page-title', 'Create Category')

@section('content')

<div class="space-y-5">

<!-- Header -->
<div class="flex justify-between">
    <h2 class="text-lg font-semibold">Create Category</h2>
    <a href="{{ route('admin.categories.index') }}">← Back</a>
</div>

<!-- Form -->
<div class="bg-white p-5 rounded-xl border max-w-xl">

    <form id="createForm">

        @csrf

        <input type="text" name="name" id="name"
               placeholder="Category name"
               class="border w-full px-3 py-2 rounded">

        <p class="text-xs mt-1 text-slate-500">
            Slug: <span id="slugPreview">-</span>
        </p>

        <div class="mt-4 flex justify-end gap-2">
            <button type="submit"
                    class="bg-slate-900 text-white px-4 py-2 rounded">
                Save
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

// Submit AJAX
$('#createForm').on('submit', function (e) {

    e.preventDefault();

    $.ajax({
        url: "{{ route('admin.categories.store') }}",
        type: "POST",
        data: $(this).serialize(),

        success: function () {
            showToast('Category created', 'success');
            window.location.href = "{{ route('admin.categories.index') }}";
        },

        error: function () {
            showToast('Create failed', 'danger');
        }
    });

});

</script>

@endpush
