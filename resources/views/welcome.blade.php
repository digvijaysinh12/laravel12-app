<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

```
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

</head>
<body class="bg-light">

```
<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('products.index') }}">
            Product Admin
        </a>

        <div class="ms-auto text-white">
            Welcome, {{ $current_user->name ?? 'Guest' }}
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    @yield('content')
</div>
```

</body>
</html>
