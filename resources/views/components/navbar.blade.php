@props(['cartCount' => 0, 'minimal' => false])

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">{{ config('app.name') }}</a>

        <div class="ms-auto d-flex align-items-center gap-2">
            @auth
                @unless($minimal)
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark btn-sm">Dashboard</a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Products</a>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm">
                        Cart ({{ $cartCount ?? 0 }})
                    </a>
                @endunless

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm">Login</a>
                <a href="{{ route('register') }}" class="btn btn-dark btn-sm">Register</a>
            @endauth
        </div>
    </div>
</nav>
