@props(['title' => null])

<div class="bg-white rounded-xl border shadow-sm">

    @if($title)
        <div class="p-4 border-b font-medium">
            {{ $title }}
        </div>
    @endif

    <div class="p-4">
        {{ $slot }}
    </div>

</div>