@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto">
    <x-card>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Overtime</h1>
                <p class="text-gray-500">Manage your overtime session</p>
            </div>

            @if($isWeekend)
            <span class="px-4 py-2 rounded-full bg-yellow-100 text-yellow-700 text-sm font-medium">
                Weekend Overtime
            </span>
            @endif
        </div>
    </x-card>

    @if(session('success'))
    <div class="mt-4 p-4 rounded-xl bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="mt-4 p-4 rounded-xl bg-red-100 text-red-700"> {{ session('error') }}</div>
    @endif

    <x-card class="mt-6">
        <div class="text-center">
            <p class="text-gray-500 mb-2">Overtime Duration</p>
            <h2 id="overtime-timer" class="text-5xl font-bold">00:00:00</h2>
        </div>
    </x-card>


    @if(!$activeOvertime)
    <x-card class="mt-6">

        <form
            action="{{ route('overtimes.start') }}"
            method="POST">

            @csrf

            <div class="space-y-5">

                <div>

                    <label
                        class="block mb-2 font-medium">

                        Task

                    </label>

                    <select
                        name="id_task"
                        class="w-full rounded-xl border border-border p-3"
                        required>

                        <option value="">

                            Select Task

                        </option>

                        @foreach($tasks as $task)

                        <option
                            value="{{ $task->id_task }}">

                            {{ $task->nama_task }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label
                        class="block mb-2 font-medium">

                        Reason

                    </label>

                    <textarea
                        name="reason"
                        rows="4"
                        class="w-full rounded-xl border border-border p-3"
                        required></textarea>

                </div>

            </div>

            <div class="mt-6">

                <button
                    type="submit"
                    class="px-6 py-3 rounded-xl bg-black text-white">

                    Start Overtime

                </button>

            </div>

        </form>

    </x-card>

    @else

    <x-card class="mt-6">

        <div class="space-y-4">

            <div>

                <span class="font-medium">

                    Task :

                </span>

                {{ $activeOvertime->task?->nama_task }}

            </div>

            <div>

                <span class="font-medium">

                    Started :

                </span>

                {{ \Carbon\Carbon::parse($activeOvertime->start_overtime)->format('d M Y H:i:s') }}

            </div>

            <div>

                <span class="font-medium">

                    Status :

                </span>

                <span class="text-green-600">

                    Overtime Running

                </span>

            </div>

        </div>

        <form
            action="{{ route('overtimes.stop') }}"
            method="POST"
            class="mt-6">

            @csrf

            <button
                class="px-6 py-3 rounded-xl bg-black text-white">

                Finish Overtime

            </button>

        </form>

    </x-card>

    @endif

</div>

@endsection

@if($activeOvertime)

@push('scripts')

<script>

let startTime =
    new Date(
        "{{ \Carbon\Carbon::parse($activeOvertime->start_overtime)->format('Y-m-d H:i:s') }}"
    );

function updateTimer()
{
    let now =
        new Date();

    let diff =
        now - startTime;

    let hours =
        Math.floor(
            diff / 1000 / 60 / 60
        );

    let minutes =
        Math.floor(
            diff / 1000 / 60
        ) % 60;

    let seconds =
        Math.floor(
            diff / 1000
        ) % 60;

    document
        .getElementById(
            'overtime-timer'
        )
        .innerHTML =

        String(hours).padStart(2,'0')
        + ':'
        + String(minutes).padStart(2,'0')
        + ':'
        + String(seconds).padStart(2,'0');
}

updateTimer();

setInterval(
    updateTimer,
    1000
);

</script>

@endpush

@else

@push('scripts')

<script>

document
    .getElementById(
        'overtime-timer'
    )
    .innerHTML =
        '00:00:00';

</script>

@endpush

@endif