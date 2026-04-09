@extends('admin.layouts.app')

@section('page-title', 'Coupons')

@section('content')
<x-admin.card title="Coupons" description="Marketing and discount codes.">
    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center text-sm text-slate-500">
        No coupons configured yet.
    </div>
</x-admin.card>
@endsection
