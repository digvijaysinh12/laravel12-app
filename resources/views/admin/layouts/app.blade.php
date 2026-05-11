<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-user-id" content="{{ auth()->id() }}">
    <meta name="auth-user-role" content="{{ auth()->user()?->role }}">
    <title>{{ config('app.name') }} - @yield('page-title', 'Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden bg-slate-50 text-slate-900 antialiased">
<div class="flex h-full">
    <aside class="flex w-64 flex-col border-r border-slate-200 bg-white">
        <div class="shrink-0 border-b px-5 py-5">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-slate-900">
                {{ config('app.name') }}
            </a>
            <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-500">
                Admin panel
            </p>
        </div>

        <div class="flex-1 overflow-y-auto">
            <x-admin.sidebar />
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 border-b bg-white px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">
                    @yield('page-title', 'Dashboard')
                </h1>

                <x-navbar.admin-navbar :notification-data="$adminNotificationData" />
            </div>
        </header>

        <main id="main-content" class="flex-1 space-y-4 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<div id="toast-container" class="fixed right-5 top-5 z-50 space-y-2"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function showToast(message, tone = 'success') {
    const classes = {
        success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        danger: 'border-rose-200 bg-rose-50 text-rose-800',
        warning: 'border-amber-200 bg-amber-50 text-amber-800',
        info: 'border-blue-200 bg-blue-50 text-blue-800'
    };

    const icons = {
        success: 'OK',
        danger: 'X',
        warning: '!',
        info: 'i'
    };

    const toast = `
        <div class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-sm ${classes[tone]}">
            <span class="font-bold">${icons[tone]}</span>
            <span>${message}</span>
        </div>
    `;

    const container = document.getElementById('toast-container');
    if (!container) return;

    container.insertAdjacentHTML('beforeend', toast);

    setTimeout(() => {
        const firstToast = container.firstElementChild;
        if (firstToast) {
            firstToast.style.opacity = '0';
            setTimeout(() => firstToast.remove(), 300);
        }
    }, 2500);
}

@if(session('success'))
document.addEventListener('DOMContentLoaded', function () {
    showToast(@json(session('success')), 'success');
});
@endif

@if(session('error'))
document.addEventListener('DOMContentLoaded', function () {
    showToast(@json(session('error')), 'danger');
});
@endif

@if(session('status'))
document.addEventListener('DOMContentLoaded', function () {
    showToast(@json(session('status')), 'info');
});
@endif

$(document).on('click', '.sidebar-link', function (e) {
    e.preventDefault();

    const url = $(this).attr('href');

    $('.sidebar-link')
        .removeClass('bg-slate-900 text-white')
        .addClass('text-slate-600');

    $(this)
        .addClass('bg-slate-900 text-white')
        .removeClass('text-slate-600');

$('#main-content').html(`
    <div class="flex items-center justify-center h-40">
        <div class="w-8 h-8 border-2 border-black border-t-transparent rounded-full animate-spin"></div>
    </div>
`);

    $.get(url, function (response) {
        const newContent = $(response).find('#main-content').html();
        $('#main-content').html(newContent);
        window.history.pushState({}, '', url);
    });
});
</script>

@stack('scripts')

</body>
</html>
