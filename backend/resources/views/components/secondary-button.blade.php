<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline btn-sm']) }}>
    {{ $slot }}
</button>
