@props(['messages'])

@if ($messages)
    <p class="text-sm text-red-600 mt-1">
        {{ is_array($messages) ? $messages[0] : $messages }}
    </p>
@endif