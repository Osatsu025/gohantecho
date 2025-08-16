@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success font-medium text-sm']) }}>
        {{ $status }}
    </div>
@endif
