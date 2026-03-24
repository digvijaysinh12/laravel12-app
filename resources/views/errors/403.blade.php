@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">

    <h3>403 - Forbidden</h3>

    <p class="text-muted">
        You do not have permission to access this page.
    </p>

    <a href="{{ route('dashboard') }}" class="btn btn-dark mt-3">
        Go Back
    </a>

</div>
@endsection