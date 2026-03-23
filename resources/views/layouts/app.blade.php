<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-light">

    @include('partials.navbar')

    <div class="container mt-4">
        @yield('content')
    </div>

</body>
</html>