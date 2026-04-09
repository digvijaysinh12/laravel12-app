@php
    $wrapper = $attributes->merge(['class' => 'w-full text-sm']);
@endphp

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table {{ $wrapper }}>
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    @foreach ($headers as $header)
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
