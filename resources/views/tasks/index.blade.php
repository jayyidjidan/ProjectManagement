@extends('layouts.app')

@section('content')

@if(session('success'))
<div
    class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
{{ session('success') }}
</div>
@endif

{{-- TAMBAHKAN BLOK ERROR INI --}}
@if($errors->any())
<div class="mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-700">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="flex items-end justify-between mb-6">

    <div>
    @if(isset($currentProject) && $currentProject)
    <div class="flex items-center gap-4">
        <a href="{{ route('projects.show', $currentProject) }}">
            <x-button-secondary>
                    ← Back
            </x-button-secondary>
        </a>
        <div class="flex flex-col items-start">
            <h1 class="text-4xl font-bold capitalize">
                {{ $currentProject->nama_proyek }} Project
            </h1>
            <p class="text-gray-500 mt-2">
                Manage tasks for {{ $currentProject->nama_proyek }}
            </p>
        </div>
    </div>
    @else
        <h1 class="text-4xl font-bold">
            Tasks
        </h1>
        <p class="text-gray-500 mt-2">
            Manage all tasks
        </p>
    @endif
</div>

    <div class="flex items-center gap-3">

        {{-- FILTER DROPDOWN MODEL PROJECTS --}}
        <div class="relative" id="filter-container">
            <button type="button" onclick="toggleFilterDropdown()" class="px-5 py-2.5 rounded-2xl border border-border bg-white flex items-center gap-2 hover:bg-gray-50 transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filters
            </button>

            <div id="filter-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-border z-50 p-5">
                <form action="{{ route('tasks.index') }}" method="GET">
                    
                    {{-- Menyimpan parameter sort dan search agar tidak hilang saat memfilter --}}
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif

                    {{-- 1. FILTER PROJECT --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select name="project" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Projects</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id_proyek }}" @selected(request('project') == $p->id_proyek)>
                                    {{ $p->nama_proyek }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. FILTER STATUS --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Status</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->status_name }}" @selected(request('status') == $s->status_name)>
                                    {{ $s->status_name }}
                                </option>
                            @endforeach
                            <option value="Overdue" @selected(request('status') == 'Overdue')>Overdue</option>
                        </select>
                    </div>

                    {{-- 3. FILTER PRIORITY --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority" class="w-full rounded-xl border border-border p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50">
                            <option value="">All Priorities</option>
                            @foreach($priorities as $pr)
                                <option value="{{ $pr->id_priority }}" @selected(request('priority') == $pr->id_priority)>
                                    {{ $pr->priority_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('tasks.index') }}" class="text-sm text-gray-500 hover:text-gray-800 underline">
                            Clear Filter
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition">
                            Apply Filter
                        </button>
                    </div>

                </form>
            </div>
        </div>

        @php
        // Cek apakah ada filter project di URL saat ini
        $projectIdParams = request('project') ? ['project' => request('project')] : [];
        @endphp

        <a href="{{ route('tasks.create', [
            'project' => request('project'),
            'origin' => 'tasks'
        ]) }}">
            <x-button-primary>
                Add Task
            </x-button-primary>
        </a>

    </div>

</div>


{{-- SUMMARY CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">

    {{-- TOTAL TASKS (Clear Status Filter) --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="block transition hover:-translate-y-1 hover:shadow-lg">
        <x-card>
            <p class="text-gray-500 text-sm">
                Total Tasks
            </p>
            <h2 class="text-3xl font-bold mt-2">
                {{
                    $planning->count()
                    + $ongoing->count()
                    + $reviewed->count()
                    + $finished->count()
                    + $canceled->count()
                }}
            </h2>
        </x-card>
    </a>

    {{-- PLANNING --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Planning']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Planning' ? 'ring-2 ring-yellow-500 rounded-xl' : '' }}">
        <x-card>
            <p class="text-gray-500 text-sm">
                Planning
            </p>
            <h2 class="text-3xl font-bold text-yellow-600 mt-2">
                {{ $planning->count() }}
            </h2>
        </x-card>
    </a>

    {{-- ON GOING --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'On Going']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'On Going' ? 'ring-2 ring-blue-500 rounded-xl' : '' }}">
        <x-card>
            <p class="text-gray-500 text-sm">
                On Going
            </p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">
                {{ $ongoing->count() }}
            </h2>
        </x-card>
    </a>

    {{-- REVIEWED --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Reviewed']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Reviewed' ? 'ring-2 ring-purple-500 rounded-xl' : '' }}">
        <x-card>
            <p class="text-gray-500 text-sm">
                Reviewed
            </p>
            <h2 class="text-3xl font-bold text-purple-600 mt-2">
                {{ $reviewed->count() }}
            </h2>
        </x-card>
    </a>

    {{-- FINISHED --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Finished']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Finished' ? 'ring-2 ring-green-500 rounded-xl' : '' }}">
        <x-card>
            <p class="text-gray-500 text-sm">
                Finished
            </p>
            <h2 class="text-3xl font-bold text-green-600 mt-2">
                {{ $finished->count() }}
            </h2>
        </x-card>
    </a>

    {{-- OVERDUE --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Overdue']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Overdue' ? 'ring-2 ring-red-500 rounded-xl' : '' }}">
        <x-card>
            <p class="text-gray-500 text-sm">Overdue</p>
            <h2 class="text-3xl font-bold text-red-600 mt-2">{{ $overdue->count() }}</h2>
        </x-card>
    </a>

</div>

{{-- PLANNING --}}
@include('tasks.partials.status-table', [
    'title' => 'Planning',
    'tasks' => $planning,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
    'members' => $members
])

{{-- ON GOING --}}
@include('tasks.partials.status-table', [
    'title' => 'On Going',
    'tasks' => $ongoing,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
    'members' => $members
])

{{-- REVIEWED --}}
@include('tasks.partials.status-table', [
    'title' => 'Reviewed',
    'tasks' => $reviewed,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
    'members' => $members
])

{{-- FINISHED --}}
@include('tasks.partials.status-table', [
    'title' => 'Finished',
    'tasks' => $finished,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
    'members' => $members
])

{{-- OVERDUE --}}
@include('tasks.partials.status-table', [
    'title' => 'Overdue',
    'tasks' => $overdue,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
    'members' => $members
])

{{-- CANCELED --}}
@include('tasks.partials.status-table', [
    'title' => 'Canceled',
    'tasks' => $canceled,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
    'members' => $members
])

<script>
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filter-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Menutup dropdown jika user menekan / klik sembarang area di luar kotak filter
    window.addEventListener('click', function(e) {
        const filterContainer = document.getElementById('filter-container');
        if (filterContainer && !filterContainer.contains(e.target)) {
            document.getElementById('filter-dropdown').classList.add('hidden');
        }
    });
</script>

@endsection