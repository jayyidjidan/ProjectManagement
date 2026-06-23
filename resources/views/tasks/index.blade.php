@extends('layouts.app')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-700">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- HEADLINE & FILTERS --}}
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
        {{-- FILTER DROPDOWN MODEL PROJECTS (Sama seperti sebelumnya) --}}
        <div class="relative" id="filter-container">
            <button type="button" onclick="toggleFilterDropdown()" class="px-5 py-2.5 rounded-2xl border border-border bg-white flex items-center gap-2 hover:bg-gray-50 transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filters
            </button>

            <div id="filter-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-border z-50 p-5">
                <form action="{{ route('tasks.index') }}" method="GET">
                    {{-- Hidden inputs --}}
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request('view')) <input type="hidden" name="view" value="{{ request('view') }}"> @endif {{-- Simpan state view --}}

                    {{-- FILTER PROJECT --}}
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

                    {{-- FILTER STATUS --}}
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

                    {{-- FILTER PRIORITY --}}
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

        <a href="{{ route('tasks.create', ['project' => request('project'), 'origin' => 'tasks']) }}">
            <x-button-primary>
                Add Task
            </x-button-primary>
        </a>
    </div>
</div>

{{-- VIEW TOGGLE (LIST VS KANBAN) --}}
<div class="flex items-center gap-2 mb-6 bg-gray-100 p-1.5 rounded-xl w-max border border-gray-200">
    <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" 
       class="px-5 py-2 rounded-lg text-sm font-medium transition {{ request('view', 'list') === 'list' ? 'bg-white shadow-sm text-black' : 'text-gray-500 hover:text-black' }}">
        List View
    </a>
    <a href="{{ request()->fullUrlWithQuery(['view' => 'kanban']) }}" 
       class="px-5 py-2 rounded-lg text-sm font-medium transition {{ request('view') === 'kanban' ? 'bg-white shadow-sm text-black' : 'text-gray-500 hover:text-black' }}">
        Kanban Board
    </a>
</div>

{{-- LOGIKA TAMPILAN BERDASARKAN PARAMETER 'view' --}}
@if(request('view') === 'kanban')

    {{-- ==================== KANBAN VIEW ==================== --}}
    <div class="flex overflow-x-auto gap-6 pb-6 items-start h-[calc(100vh-250px)]">
        
        {{-- Kolom: Planning --}}
        <div class="w-80 flex-shrink-0 bg-gray-50 rounded-2xl p-4 flex flex-col max-h-full border border-border">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-400"></span> Planning
                </h3>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-md font-bold">{{ $planning->count() }}</span>
            </div>
            <div class="overflow-y-auto flex-1 space-y-3 pr-1">
                @foreach($planning as $task)
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-grab hover:border-gray-400 transition">
                        <p class="font-semibold text-gray-800 text-sm mb-1">{{ $task->nama_task }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ $task->project->nama_proyek ?? 'No Project' }}</p>
                        {{-- Tambahkan detail lain seperti priority / deadline / member avatar di sini --}}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kolom: On Going --}}
        <div class="w-80 flex-shrink-0 bg-gray-50 rounded-2xl p-4 flex flex-col max-h-full border border-border">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span> On Going
                </h3>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-md font-bold">{{ $ongoing->count() }}</span>
            </div>
            <div class="overflow-y-auto flex-1 space-y-3 pr-1">
                @foreach($ongoing as $task)
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-grab hover:border-gray-400 transition">
                        <p class="font-semibold text-gray-800 text-sm mb-1">{{ $task->nama_task }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ $task->project->nama_proyek ?? 'No Project' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kolom: Reviewed --}}
        <div class="w-80 flex-shrink-0 bg-gray-50 rounded-2xl p-4 flex flex-col max-h-full border border-border">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-400"></span> Reviewed
                </h3>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-md font-bold">{{ $reviewed->count() }}</span>
            </div>
            <div class="overflow-y-auto flex-1 space-y-3 pr-1">
                @foreach($reviewed as $task)
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-grab hover:border-gray-400 transition">
                        <p class="font-semibold text-gray-800 text-sm mb-1">{{ $task->nama_task }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ $task->project->nama_proyek ?? 'No Project' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kolom: Finished --}}
        <div class="w-80 flex-shrink-0 bg-gray-50 rounded-2xl p-4 flex flex-col max-h-full border border-border">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span> Finished
                </h3>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-md font-bold">{{ $finished->count() }}</span>
            </div>
            <div class="overflow-y-auto flex-1 space-y-3 pr-1">
                @foreach($finished as $task)
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-grab hover:border-gray-400 transition">
                        <p class="font-semibold text-gray-800 text-sm mb-1">{{ $task->nama_task }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ $task->project->nama_proyek ?? 'No Project' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

@else

    {{-- ==================== LIST VIEW (TAMPILAN ASLI KAMU) ==================== --}}
    
    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="block transition hover:-translate-y-1 hover:shadow-lg">
            <x-card>
                <p class="text-gray-500 text-sm">Total Tasks</p>
                <h2 class="text-3xl font-bold mt-2">
                    {{ $planning->count() + $ongoing->count() + $reviewed->count() + $finished->count() + $canceled->count() }}
                </h2>
            </x-card>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status' => 'Planning']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Planning' ? 'ring-2 ring-yellow-500 rounded-xl' : '' }}">
            <x-card>
                <p class="text-gray-500 text-sm">Planning</p>
                <h2 class="text-3xl font-bold text-yellow-600 mt-2">{{ $planning->count() }}</h2>
            </x-card>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status' => 'On Going']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'On Going' ? 'ring-2 ring-blue-500 rounded-xl' : '' }}">
            <x-card>
                <p class="text-gray-500 text-sm">On Going</p>
                <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $ongoing->count() }}</h2>
            </x-card>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status' => 'Reviewed']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Reviewed' ? 'ring-2 ring-purple-500 rounded-xl' : '' }}">
            <x-card>
                <p class="text-gray-500 text-sm">Reviewed</p>
                <h2 class="text-3xl font-bold text-purple-600 mt-2">{{ $reviewed->count() }}</h2>
            </x-card>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status' => 'Finished']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Finished' ? 'ring-2 ring-green-500 rounded-xl' : '' }}">
            <x-card>
                <p class="text-gray-500 text-sm">Finished</p>
                <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $finished->count() }}</h2>
            </x-card>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status' => 'Overdue']) }}" class="block transition hover:-translate-y-1 hover:shadow-lg {{ request('status') == 'Overdue' ? 'ring-2 ring-red-500 rounded-xl' : '' }}">
            <x-card>
                <p class="text-gray-500 text-sm">Overdue</p>
                <h2 class="text-3xl font-bold text-red-600 mt-2">{{ $overdue->count() }}</h2>
            </x-card>
        </a>
    </div>

    {{-- TABLES --}}
    @include('tasks.partials.status-table', ['title' => 'Planning', 'tasks' => $planning, 'projects' => $projects, 'priorities' => $priorities, 'statuses' => $statuses, 'members' => $members])
    @include('tasks.partials.status-table', ['title' => 'On Going', 'tasks' => $ongoing, 'projects' => $projects, 'priorities' => $priorities, 'statuses' => $statuses, 'members' => $members])
    @include('tasks.partials.status-table', ['title' => 'Reviewed', 'tasks' => $reviewed, 'projects' => $projects, 'priorities' => $priorities, 'statuses' => $statuses, 'members' => $members])
    @include('tasks.partials.status-table', ['title' => 'Finished', 'tasks' => $finished, 'projects' => $projects, 'priorities' => $priorities, 'statuses' => $statuses, 'members' => $members])
    @include('tasks.partials.status-table', ['title' => 'Overdue', 'tasks' => $overdue, 'projects' => $projects, 'priorities' => $priorities, 'statuses' => $statuses, 'members' => $members])
    @include('tasks.partials.status-table', ['title' => 'Canceled', 'tasks' => $canceled, 'projects' => $projects, 'priorities' => $priorities, 'statuses' => $statuses, 'members' => $members])

@endif

<script>
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filter-dropdown');
        dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const filterContainer = document.getElementById('filter-container');
        if (filterContainer && !filterContainer.contains(e.target)) {
            document.getElementById('filter-dropdown').classList.add('hidden');
        }
    });
</script>

@endsection