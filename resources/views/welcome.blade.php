@extends('layouts.app')

@section('content')

<div class="text-center mt-5">
    <h1>Welcome to {{ config('app.name') }}</h1>
    <p class="text-muted">Simple Product Management App</p>

    @guest
        <a href="{{ route('login') }}" class="btn btn-primary m-2">Login</a>
        <a href="{{ route('register') }}" class="btn btn-warning m-2">Register</a>
    @endguest

    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-success mt-3">
            Go to Dashboard
        </a>
    @endauth
</div>

@endsection