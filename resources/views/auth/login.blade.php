@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif
<div class="row justify-content-center">
    <div class="col-md-4">

        <div class="card shadow p-3">
            <h4 class="text-center mb-3">Login</h4>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

                <button class="btn btn-dark w-100">Login</button>
            </form>

        </div>

    </div>
</div>

@endsection