@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <div class="flex items-end justify-between">

        <div>

            <h1 class="text-4xl font-bold">
                Attendance
            </h1>

            <p class="text-gray-500 mt-2">
                Daily attendance report
            </p>

        </div>

    </div>

    {{-- DATE NAVIGATION --}}
    <x-card>

        <div
            class="flex items-center justify-between">

            <a
                href="{{ route(
                    'attendances.index',
                    [
                        'date' =>
                        $previousDate
                    ]
                ) }}"
                class="px-4 py-2 rounded-xl border border-border hover:bg-gray-50">

                ◀

            </a>

            <div class="text-center">

                <h2 class="text-2xl font-bold">

                    {{
                        \Carbon\Carbon::parse(
                            $selectedDate
                        )->translatedFormat(
                            'l'
                        )
                    }}

                </h2>

                <p class="text-gray-500">

                    {{
                        \Carbon\Carbon::parse(
                            $selectedDate
                        )->translatedFormat(
                            'd F Y'
                        )
                    }}

                </p>

            </div>

            @if(
                $selectedDate < $today
            )

                <a
                    href="{{ route(
                        'attendances.index',
                        [
                            'date' =>
                            $nextDate
                        ]
                    ) }}"
                    class="px-4 py-2 rounded-xl border border-border hover:bg-gray-50">

                    ▶

                </a>

            @else

                <button
                    disabled
                    class="px-4 py-2 rounded-xl border border-border opacity-50 cursor-not-allowed">

                    ▶

                </button>

            @endif

        </div>

    </x-card>

    {{-- FILTER --}}
    <form
        method="GET">

        <input
            type="date"
            name="date"
            value="{{ $selectedDate }}"
            onchange="this.form.submit()"
            class="rounded-xl border border-border px-4 py-2">

    </form>

    {{-- SUMMARY --}}
    <div class="grid md:grid-cols-4 gap-4">

        <x-card>

            <p class="text-gray-500 text-sm">
                Present
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $hadir }}
            </h2>

        </x-card>

        <x-card>

            <p class="text-gray-500 text-sm">
                WFH
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $wfh }}
            </h2>

        </x-card>

        <x-card>

            <p class="text-gray-500 text-sm">
                Leave
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $cuti }}
            </h2>

        </x-card>

        <x-card>

            <p class="text-gray-500 text-sm">
                No Information
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $tanpaKeterangan }}
            </h2>

        </x-card>

    </div>

    {{-- TABLE --}}
    <x-card>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr
                        class="border-b border-border">

                        <th class="p-4 text-left">
                            Employee
                        </th>

                        <th class="p-4 text-left">
                            Position
                        </th>

                        <th class="p-4 text-left">
                            Status
                        </th>

                        <th class="p-4 text-left">
                            Check In
                        </th>

                        <th class="p-4 text-left">
                            Check Out
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse(
                        $attendances
                        as $attendance
                    )

                    <tr
                        class="border-b border-border">

                        <td class="p-4">

                            {{
                                $attendance
                                ->member
                                ?->member_name
                            }}

                        </td>

                        <td class="p-4">

                            {{
                                $attendance
                                ->member
                                ?->jabatan
                                ?->position_name
                            }}

                        </td>

                        <td class="p-4">

                            {{
                                $attendance
                                ->status
                                ?->status_name
                            }}

                        </td>

                        <td class="p-4">

                            {{
                                $attendance
                                ->start_hour
                                ??
                                '-'
                            }}

                        </td>

                        <td class="p-4">

                            {{
                                $attendance
                                ->leave_hour
                                ??
                                '-'
                            }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center py-10 text-gray-500">

                            No attendance data

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </x-card>

</div>

@endsection