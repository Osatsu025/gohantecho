@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-3 py-2 border-b-2 border-primary text-primary font-semibold focus:outline-hidden transition-colors duration-150 ease-in-out'
    : 'inline-flex items-center px-3 py-2 border-b-2 border-transparent text-base-content/60 hover:text-primary hover:border-primary focus:outline-hidden transition-colors duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
