@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <x-card>
        <h1 class="text-3xl font-bold mb-2">
            Overtime Approvals
        </h1>
        <p class="text-gray-500">
            Pending overtime requests
        </p>
    </x-card>

    @if(session('success'))
    <div class="mt-4 p-4 rounded-xl bg-green-100 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <x-card class="mt-6 overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-gray-700 text-sm font-semibold">
                    <th class="text-left px-4 py-3">Employee</th>
                    <th class="text-left px-4 py-3">Date</th>
                    <th class="text-left px-4 py-3">Task</th>
                    <th class="text-left px-4 py-3">Duration</th>
                    <th class="text-left px-4 py-3">Reason</th>
                    <th class="text-left px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-600">
                @forelse($overtimes as $overtime)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $overtime->member->member_name }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($overtime->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $overtime->task?->nama_task ?? '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $overtime->durasi_jam }} Hours
                    </td>
                    <td class="px-4 py-3 max-w-xs break-words">
                        {{ $overtime->reason }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <form action="{{ route('overtimes.approve', $overtime->id_riwayat) }}" method="POST">
                                @csrf
                                <button class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold shadow-sm transition">
                                    Approve
                                </button>
                            </form>

                            <form action="{{ route('overtimes.reject', $overtime->id_riwayat) }}" method="POST">
                                @csrf
                                <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm transition">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">
                        No pending overtime
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            {{ $overtimes->links() }}
        </div>
    </x-card>
</div>
@endsection