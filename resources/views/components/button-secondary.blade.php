<button
    {{ $attributes->merge([
        'class' =>
            'h-12 px-6 rounded-2xl border border-border bg-white'
    ]) }}>
    {{ $slot }}
</button>