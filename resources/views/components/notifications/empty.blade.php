@props([
    'title' => 'No Notifications',
    'message' => "You're all caught up.",
    'class' => '',
])

<div class="{{ $class }}">
    <div class="mb-3 text-4xl">🔔</div>
    <h4 class="text-sm font-semibold text-slate-700">{{ $title }}</h4>
    <p class="mt-1 text-xs text-slate-500">{{ $message }}</p>
</div>
