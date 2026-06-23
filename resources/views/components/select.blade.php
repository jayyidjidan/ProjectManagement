<select
    {{ $attributes->merge([
        'class' =>
            'w-full h-12 px-4 rounded-2xl border border-border bg-white'
    ]) }}>

    {{ $slot }}

</select>