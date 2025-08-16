@props(['active'])

@php
$classes = ($active ?? false)
            ? 'menu-item active'
            : 'menu-item';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
