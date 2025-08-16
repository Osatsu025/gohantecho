@props(['value'])

<label {{ $attributes->merge(['class' => 'label font-medium text-sm text-base-content']) }}>
    {{ $value ?? $slot }}
</label>
