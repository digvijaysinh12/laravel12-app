@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-4">

        <div class="card shadow p-3">
            <h4 class="text-center mb-3">Register</h4>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <input type="text" name="name" class="form-control mb-3" placeholder="Name" required>

                <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

                <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Confirm Password" required>

                <button class="btn btn-warning w-100">Register</button>
            </form>

        </div>

    </div>
</div>

@endsection