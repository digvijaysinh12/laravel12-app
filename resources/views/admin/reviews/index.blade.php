@extends('admin.layouts.app')

@section('page-title', 'Reviews')

@section('content')
<x-admin.card title="Reviews" description="Customer review moderation placeholder.">
    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center text-sm text-slate-500">
        No reviews loaded yet.
    </div>
</x-admin.card>
@endsection
