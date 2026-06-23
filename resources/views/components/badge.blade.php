@props([
    'type' => 'info'
])

@php

$classes = match($type){

    'success'
        => 'bg-success/10 text-success',

    'warning'
        => 'bg-warning/10 text-warning',

    'error'
        => 'bg-error/10 text-error',

    default
        => 'bg-info/10 text-info'
};

@endphp

<span
class="px-3 py-1 rounded-full text-sm {{ $classes }}">
    {{ $slot }}
</span>