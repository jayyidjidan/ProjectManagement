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
{{-- FILTER DROPDOWN BARU --}}
    <div class="flex items-center justify-end mb-4">
        <div class="relative" id="filter-container">
            <button type="button" onclick="toggleFilterDropdown()" class="px-5 py-2.5 rounded-2xl border border-border bg-white flex items-center gap-2 hover:bg-gray-50 transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filters
            </button>

            <div id="filter-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-border z-50 p-5">
                <form action="{{ route('attendances.index') }}" method="GET">
                    
                    {{-- 1. FILTER TANGGAL --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" value="{{ $selectedDate }}" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                    </div>

                    {{-- 2. FILTER POSITION --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <select name="position" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id_position }}" @selected(request('position') == $position->id_position)>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. FILTER STATUS --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id_status }}" @selected(request('status') == $status->id_status)>
                                    {{ $status->status_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center justify-between mt-6">
                        {{-- Clear filter tetap menyimpan tanggal agar tidak loncat ke hari ini --}}
                        <a href="{{ route('attendances.index', ['date' => $selectedDate]) }}" class="text-sm text-gray-500 hover:text-gray-800 underline">
                            Clear Filter
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition">
                            Apply Filter
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

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

{{-- SCRIPT JAVASCRIPT UNTUK TOGGLE DROPDOWN --}}
<script>
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filter-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Auto-close ketika klik di luar area dropdown filter
    window.addEventListener('click', function(e) {
        const filterContainer = document.getElementById('filter-container');
        const filterDropdown = document.getElementById('filter-dropdown');
        
        if (filterContainer && !filterContainer.contains(e.target) && !filterDropdown.classList.contains('hidden')) {
            filterDropdown.classList.add('hidden');
        }
    });
</script>
@endsection