@extends('admin.layouts.app')

@section('page-title', 'Cache Monitor')

@section('content')

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@include('admin.cache.partials.stats')
@include('admin.cache.partials.actions')

@endsection