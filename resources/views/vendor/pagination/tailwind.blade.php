@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-2">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-gray-400 border rounded">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1 border rounded hover:bg-gray-100">Prev</a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-1">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 text-white bg-blue-500 rounded">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1 border rounded hover:bg-gray-100">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1 border rounded hover:bg-gray-100">Next</a>
        @else
            <span class="px-3 py-1 text-gray-400 border rounded">Next</span>
        @endif

    </nav>
@endif