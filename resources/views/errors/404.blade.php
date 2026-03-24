@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h3>404 - Page Not Found</h3>
    <p class="text-muted">The page you are looking for does not exist.</p>

    <a href="{{ route('dashboard') }}" class="btn btn-dark mt-3">
        Go Back
    </a>
</div>
@endsection