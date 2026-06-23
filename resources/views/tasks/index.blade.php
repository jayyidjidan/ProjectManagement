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

        {{-- FILTER --}}
        <form
            method="GET"
            class="flex gap-3">

            <select
                name="status"
                onchange="this.form.submit()"
                class="rounded-xl border border-border px-4 py-2">

                <option value="">
                    All Status
                </option>

                <option
                    value="Planning"
                    @selected(request('status') == 'Planning')>

                    Planning

                </option>

                <option
                    value="On Going"
                    @selected(request('status') == 'On Going')>

                    On Going

                </option>

                <option
                    value="Reviewed"
                    @selected(request('status') == 'Reviewed')>

                    Reviewed

                </option>

                <option
                    value="Finished"
                    @selected(request('status') == 'Finished')>

                    Finished

                </option>

                <option
                    value="Canceled"
                    @selected(request('status') == 'Canceled')>

                    Canceled

                </option>

            </select>

            {{-- SORT --}}
            <select
                name="sort"
                onchange="this.form.submit()"
                class="rounded-xl border border-border px-4 py-2">

                <option
                    value="nama_task"
                    @selected(request('sort') == 'nama_task')>

                    Task Name

                </option>

                <option
                    value="deadline_task"
                    @selected(request('sort') == 'deadline_task')>

                    Deadline

                </option>

                <option
                    value="created_at"
                    @selected(request('sort') == 'created_at')>

                    Created Date

                </option>

            </select>

        </form>

            @php
            // Cek apakah ada filter project_id di URL saat ini
            $projectIdParams = request('project_id') ? ['project_id' => request('project_id')] : [];
            @endphp

            <a href="{{ route('tasks.create', [
                'project_id' => request('project_id'),
                'origin' => 'tasks' // Tambahkan ini
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

@endsection
