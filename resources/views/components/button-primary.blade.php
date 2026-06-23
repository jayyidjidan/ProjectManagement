<button
    {{ $attributes->merge([
        'class' =>
            'h-12 px-6 rounded-2xl text-white font-normal'
    ]) }}
    style="
        background: linear-gradient(
        99deg,
        #FF5700 9.65%,
        #FF894C 90.35%
        );
    ">
    {{ $slot }}
</button>