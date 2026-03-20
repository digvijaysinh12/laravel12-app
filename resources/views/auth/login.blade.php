@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-4">

        <div class="card shadow">
            <div class="card-body">

                <h4 class="mb-3 text-center">Login</h4>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    {{-- Remember --}}
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input">
                        <label class="form-check-label">Remember me</label>
                    </div>

                    {{-- Button --}}
                    <button class="btn btn-dark w-100">
                        Login
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection 