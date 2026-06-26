@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-4xl font-bold">Member Dashboard</h1>
        <p class="text-gray-500 mt-2">Welcome back! Here is your personal task summary and work performance statistics.</p>
    </div>

    {{-- TOP STATS GRID: 4 KONTEN UTAMA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Total Task --}}
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-700 text-sm font-semibold uppercase tracking-wider">Total Tasks</p>
                    <h2 class="text-4xl font-extrabold text-blue-900 mt-2">
                        {{ $totalTasks }} <span class="text-lg font-medium text-blue-600">Assigned</span>
                    </h2>
                </div>
                <span class="p-3 bg-blue-500 text-white rounded-xl shadow-md shadow-blue-200">
                    📋
                </span>
            </div>
        </div>

        {{-- Overdue Task --}}
        <div class="bg-gradient-to-br from-red-50 to-rose-50 border border-rose-200 rounded-2xl p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-rose-700 text-sm font-semibold uppercase tracking-wider">Overdue Tasks</p>
                    <h2 class="text-4xl font-extrabold text-rose-900 mt-2">
                        {{ $overdueTasks }} <span class="text-lg font-medium text-rose-600">Late</span>
                    </h2>
                </div>
                <span class="p-3 bg-rose-500 text-white rounded-xl shadow-md shadow-rose-200">
                    ⚠️
                </span>
            </div>
        </div>

        {{-- Upcoming Deadline Task --}}
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-700 text-sm font-semibold uppercase tracking-wider">Upcoming Deadlines</p>
                    <h2 class="text-4xl font-extrabold text-amber-900 mt-2">
                        {{ $upcomingTasks }} <span class="text-lg font-medium text-amber-600">Within 7d</span>
                    </h2>
                </div>
                <span class="p-3 bg-amber-500 text-white rounded-xl shadow-md shadow-amber-200">
                    ⏰
                </span>
            </div>
        </div>

        {{-- Total Work Hours --}}
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-emerald-200 rounded-2xl p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-emerald-700 text-sm font-semibold uppercase tracking-wider">Total Work Hours</p>
                    <h2 class="text-4xl font-extrabold text-emerald-900 mt-2">
                        {{ $totalWorkHours }} <span class="text-lg font-medium text-emerald-600">Hours</span>
                    </h2>
                </div>
                <span class="p-3 bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-200">
                    ⏱️
                </span>
            </div>
        </div>

    </div>

    {{-- MIDDLE SECTION: WORK HOUR GRAPHIC --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Grafik Jam Kerja (7 Hari Terakhir) --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Work Hours Trends <span class="text-sm font-normal text-gray-500">(Last 7 Days)</span></h3>
                <span class="px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                    Performance Stats
                </span>
            </div>
            <div class="relative h-80">
                <canvas id="memberWorkHourChart"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('memberWorkHourChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartDataMember) !!};

        new Chart(ctx, {
            type: 'line', // Tetap pakai tipe line seperti grafik admin biar seragam dan estetik
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [{
                    label: 'Hours Worked',
                    data: dataValues.length ? dataValues : [0],
                    borderColor: '#10b981', // Pakai warna emerald green biar beda dari admin
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false } 
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            callback: function(value) { return value + ' hrs'; }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection