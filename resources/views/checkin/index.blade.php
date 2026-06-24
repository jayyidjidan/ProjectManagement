@extends('layouts.app')

@section('content')


@if(session('show_overtime_prompt'))

<div
    x-data="{ open:true }">

    <div
        x-show="open"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div
            class="bg-white rounded-2xl p-6 w-full max-w-md">

            <h2
                class="text-xl font-bold mb-3">

                Overtime Confirmation

            </h2>

            <p
                class="text-gray-500 mb-6">

                Working hours have ended.

                Do you want to continue with overtime?

            </p>

            <div
                class="flex justify-end gap-3">

                <form
                    action="{{ route('checkin.end') }}"
                    method="POST">

                    @csrf

                    <input
                        type="hidden"
                        name="force_checkout"
                        value="1">

                    <button
                        class="px-4 py-2 border rounded-xl">

                        No

                    </button>

                </form>

                <a
                    href="{{ route('overtimes.index') }}"
                    class="px-4 py-2 bg-black text-white rounded-xl">

                    Yes

                </a>

            </div>

        </div>

    </div>

</div>

@endif

<div class="max-w-4xl mx-auto">

    <x-card>

        <div class="flex justify-between items-center">

            <div>

                <h1 class="text-3xl font-bold">
                    Check In
                </h1>

                <p class="text-gray-500">
                    Attendance Today
                </p>

            </div>

            <div
                id="stopwatch"
                class="text-2xl font-bold">

                00:00:00

            </div>

        </div>

    </x-card>

    @if(session('success'))

        <div
            class="mt-4 rounded-xl bg-green-100 p-4 text-green-700">

            {{ session('success') }}

        </div>

    @endif

    @if(session('error'))

        <div
            class="mt-4 rounded-xl bg-red-100 p-4 text-red-700">

            {{ session('error') }}

        </div>

    @endif

    <x-card class="mt-6">

        <table class="w-full">

            <tr>

                <td class="py-2 font-medium">
                    Date
                </td>

                <td>
                    {{ now()->format('d M Y') }}
                </td>

            </tr>

            <tr>

                <td class="py-2 font-medium">
                    Check In
                </td>

                <td>

                    {{ $attendance?->start_hour ?? '-' }}

                </td>

            </tr>

            <tr>

                <td class="py-2 font-medium">
                    Check Out
                </td>

                <td>

                    {{ $attendance?->leave_hour ?? '-' }}

                </td>

            </tr>

            <tr>

                <td class="py-2 font-medium">
                    Status
                </td>

                <td>

                    {{ $attendance?->status?->status_name ?? '-' }}

                </td>

            </tr>

        </table>

    </x-card>

    @if(!$attendance?->start_hour)



        <form
            method="POST"
            action="{{ route('checkin.start') }}">

            @csrf

            <div class="mt-6">

                <button
                    class="px-6 py-3 bg-black text-white rounded-xl">

                    Check In

                </button>

            </div>

        </form>

    

    @endif

    @if(
        $attendance &&
        $attendance->start_hour &&
        !$attendance->leave_hour
    )

    <x-card class="mt-6">

        <form
            method="POST"
            action="{{ route('checkin.end') }}">

            @csrf

            <button
                class="px-6 py-3 bg-black text-white rounded-xl">

                Check Out

            </button>

        </form>

    </x-card>

    @endif

</div>

@endsection

@section('scripts')

<script>

let startHour =
"{{ $attendance?->start_hour }}";

if(startHour)
{
    let start =
        new Date();

    let parts =
        startHour.split(':');

    start.setHours(parts[0]);
    start.setMinutes(parts[1]);
    start.setSeconds(parts[2]);

    setInterval(function(){

        let now =
            new Date();

        let diff =
            now - start;

        let h =
            Math.floor(
                diff / 1000 / 60 / 60
            );

        let m =
            Math.floor(
                diff / 1000 / 60
            ) % 60;

        let s =
            Math.floor(
                diff / 1000
            ) % 60;

        document
            .getElementById(
                'stopwatch'
            )
            .innerHTML =
                String(h).padStart(2,'0')
                + ':'
                + String(m).padStart(2,'0')
                + ':'
                + String(s).padStart(2,'0');

    },1000);
}

</script>

@endsection