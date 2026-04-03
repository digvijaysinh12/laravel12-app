@props(['title' => null, 'actions' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $actions)
        <div class="card__header">
            <div class="card__title">{{ $title }}</div>
            @if($actions)
                <div class="card__actions">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    <div class="card__body">
        {{ $slot }}
    </div>
</div>
