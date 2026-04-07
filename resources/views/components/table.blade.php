@props(['headers' => []])

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        @if(!empty($headers))
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    @foreach($headers as $header)
                        <th class="p-3 text-left">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y">
            {{ $slot }}
        </tbody>
    </table>
</div>