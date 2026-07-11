@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <x-card>
        <div>
            <h1 class="text-3xl font-bold mb-2">
                All Overtime History
            </h1>
            <p class="text-gray-500">
                Log riwayat pengajuan overtime pegawai
            </p>
        </div>
    </x-card>

    {{-- DATE NAVIGATION --}}
    <x-card>
        <div class="flex items-center justify-between">

            <a href="{{ route('overtimes.all_history', array_merge(request()->except(['date', 'page']), ['date' => $previousDate])) }}"
               class="px-4 py-2 rounded-xl border border-border hover:bg-gray-50">
                ◀
            </a>

            <div class="text-center">
                <h2 class="text-2xl font-bold">
                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l') }}
                </h2>
                <p class="text-gray-500">
                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                </p>
            </div>

            @if($selectedDate < $today)
                <a href="{{ route('overtimes.all_history', array_merge(request()->except(['date', 'page']), ['date' => $nextDate])) }}"
                   class="px-4 py-2 rounded-xl border border-border hover:bg-gray-50">
                    ▶
                </a>
            @else
                <button disabled class="px-4 py-2 rounded-xl border border-border opacity-50 cursor-not-allowed">
                    ▶
                </button>
            @endif

        </div>
    </x-card>

    {{-- FILTER DROPDOWN --}}
    <div class="flex items-center justify-end">
        <div class="relative" id="filter-container">
            <button type="button" onclick="toggleFilterDropdown()" class="px-5 py-2.5 rounded-2xl border border-border bg-white flex items-center gap-2 hover:bg-gray-50 transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filters
            </button>

            <div id="filter-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-border z-50 p-5">
                <form action="{{ route('overtimes.all_history') }}" method="GET">

                    {{-- FILTER DATE --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                    </div>

                    {{-- FILTER MONTH --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                        <select name="month" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Months</option>
                            @foreach($months as $number => $name)
                                <option value="{{ $number }}" @selected(request('month') == $number)>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FILTER YEAR --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                        <select name="year" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Years</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected(request('year') == $year)>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FILTER STATUS --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Statuses</option>
                            <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                            <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                            <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                        </select>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('overtimes.all_history') }}" class="text-sm text-gray-500 hover:text-gray-800 underline">
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

    {{-- TABEL DATA --}}
    <x-card class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-gray-700 text-sm font-semibold">
                    <th class="text-left px-4 py-3">Employee</th>
                    <th class="text-left px-4 py-3">Project & Task</th>
                    <th class="text-left px-4 py-3">Date</th>
                    <th class="text-left px-4 py-3">Duration</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Action Date</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-600">
                @forelse($histories as $history)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Pegawai --}}
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $history->member?->member_name ?? 'Unknown' }}
                    </td>

                    {{-- Task & Project --}}
                    <td class="px-4 py-3">
                        <div class="font-medium text-black">
                            {{ $history->task?->nama_task ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $history->task?->project?->nama_proyek ?? 'No Project' }}
                        </div>
                    </td>

                    {{-- Tanggal Overtime --}}
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($history->tanggal)->translatedFormat('d M Y') }}
                    </td>

                    {{-- Durasi --}}
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $history->durasi_jam }} Hours
                    </td>

                    {{-- Status Badge --}}
                    <td class="px-4 py-3">
                        @if($history->status_approval == 'approved')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                Approved
                            </span>
                        @elseif($history->status_approval == 'rejected')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                Rejected
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                Pending
                            </span>
                        @endif
                    </td>

                    {{-- Tanggal di-Approve/Reject --}}
                    <td class="px-4 py-3 whitespace-nowrap text-gray-500 text-xs">
                        @if($history->approved_at)
                            {{ \Carbon\Carbon::parse($history->approved_at)->format('d M Y H:i') }}
                        @else
                            -
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-400">
                        Tidak ada data riwayat overtime.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $histories->links() }}
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