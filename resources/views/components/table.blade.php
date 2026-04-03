@props(['headers' => []])

<div class="table-responsive">
    <table {{ $attributes->merge(['class' => 'table align-middle']) }}>
        @if(!empty($headers))
            <thead class="table-light">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
