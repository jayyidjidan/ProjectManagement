@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-4xl font-bold">Dashboard Overview</h1>
        <p class="text-gray-500 mt-2">Welcome back! Here is your business and project development performance summary.</p>
    </div>

    {{-- TOP STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Total Projects --}}
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-emerald-200 rounded-2xl p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-emerald-700 text-sm font-semibold uppercase tracking-wider">Total Projects</p>
                    <h2 class="text-4xl font-extrabold text-emerald-900 mt-2">
                        {{ $totalProjects }} <span class="text-lg font-medium text-emerald-600">Projects</span>
                    </h2>
                </div>
                <span class="p-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200">
                    📂
                </span>
            </div>
        </div>

        {{-- Total Clients --}}
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-700 text-sm font-semibold uppercase tracking-wider">Active Clients</p>
                    <h2 class="text-4xl font-extrabold text-blue-900 mt-2">
                        {{ $totalClients }} <span class="text-lg font-medium text-blue-600">Companies</span>
                    </h2>
                </div>
                <span class="p-3 bg-blue-500 text-white rounded-xl shadow-md shadow-blue-200">
                    💼
                </span>
            </div>
        </div>
    </div>

    {{-- MIDDLE SECTION: CHART & ATTENDANCE --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Grafik Pertumbuhan Project --}}
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold mb-4">Project Growth Trends</h3>
            <div class="relative h-72">
                <canvas id="projectGrowthChart"></canvas>
            </div>
        </div>

        {{-- Attendance Hari Ini --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 h-full flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Today's Attendance</h3>
                <span class="px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                    {{ now()->format('d M Y') }}
                </span>
            </div>
            
            <div class="flex-1 overflow-y-auto space-y-3 max-h-72 pr-1">
                @forelse($attendanceToday as $attend)
                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700">
                                {{ strtoupper(substr($attend->member->member_name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm">{{ $attend->member->member_name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            {{ $attend->status->status_name ?? 'Hadir' }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400 flex flex-col items-center justify-center h-full">
                        <span class="text-3xl mb-2">🗓️</span>
                        <p class="text-sm">No attendance recorded yet for today.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- BOTTOM SECTION: DEADLINES --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-red-900 flex items-center gap-2">
                    🚨 Urgent Deadlines <span class="text-sm font-normal text-gray-500">(Tasks due within 5 days)</span>
                </h3>
            </div>
            <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">
                {{ $upcomingDeadlines->count() }} Critical Issues
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-gray-500">
                        <th class="p-4 text-left font-medium rounded-tl-xl">Task Name</th>
                        <th class="p-4 text-left font-medium">Project</th>
                        <th class="p-4 text-left font-medium">Status</th>
                        <th class="p-4 text-left font-medium">Days Left</th>
                        <th class="p-4 text-left font-medium rounded-tr-xl">Deadline Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingDeadlines as $task)
                        @php
                            $daysLeft = ceil(now()->diffInDays(\Carbon\Carbon::parse($task->deadline_task), false));
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-red-50 transition-colors">
                            <td class="p-4 font-semibold text-gray-800">{{ $task->nama_task }}</td>
                            <td class="p-4 text-gray-500">{{ $task->project->nama_proyek ?? '-' }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    {{ $task->status->status_name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-4 font-bold text-red-600">
                                @if($daysLeft == 0)
                                    Today
                                @elseif($daysLeft < 0)
                                    Overdue ({{ abs($daysLeft) }}d)
                                @else
                                    {{ $daysLeft }} Days Left
                                @endif
                            </td>
                            <td class="p-4 text-gray-600 font-medium">
                                {{ \Carbon\Carbon::parse($task->deadline_task)->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                🎉 Brilliant! No tasks or projects are nearing their deadline.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('projectGrowthChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartData) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [{
                    label: 'New Projects',
                    data: dataValues.length ? dataValues : [0],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endsection