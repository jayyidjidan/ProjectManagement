<div
    {{ $attributes->merge([
        'class' =>
            'bg-white border border-border rounded-2xl p-6'
    ]) }}>
    {{ $slot }}
</div>