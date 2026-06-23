@props([
    'title'
])

<div
class="flex items-center justify-between mb-6">

    <h1
    class="text-2xl font-bold">
        {{ $title }}
    </h1>

    <div>
        {{ $actions ?? '' }}
    </div>

</div>