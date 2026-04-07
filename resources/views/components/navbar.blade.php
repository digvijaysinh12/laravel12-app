@props([
    'mode' => 'user',
    'cartCount' => 0,
    'title' => null,
])

<x-topbar :mode="$mode" :cart-count="$cartCount" :title="$title" />
