@extends('layouts.app')

@section('content')

@if(session('success'))

<div
    class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">


{{ session('success') }}


</div>

@endif

<div class="flex items-end justify-between mb-6">

    <div>

        <h1 class="text-4xl font-bold">
            My Tasks
        </h1>

        <p class="text-gray-500 mt-2">
            Manage your assigned tasks
        </p>

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

    </div>

</div>

{{-- SUMMARY CARDS --}}

<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">


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

<x-card>
    <p class="text-gray-500 text-sm">
        Planning
    </p>

    <h2 class="text-3xl font-bold text-yellow-600 mt-2">
        {{ $planning->count() }}
    </h2>
</x-card>

<x-card>
    <p class="text-gray-500 text-sm">
        On Going
    </p>

    <h2 class="text-3xl font-bold text-blue-600 mt-2">
        {{ $ongoing->count() }}
    </h2>
</x-card>

<x-card>
    <p class="text-gray-500 text-sm">
        Reviewed
    </p>

    <h2 class="text-3xl font-bold text-purple-600 mt-2">
        {{ $reviewed->count() }}
    </h2>
</x-card>

<x-card>
    <p class="text-gray-500 text-sm">
        Finished
    </p>

    <h2 class="text-3xl font-bold text-green-600 mt-2">
        {{ $finished->count() }}
    </h2>
</x-card>


</div>

{{-- PLANNING --}}
@include('tasks.partials.my-status-table', [
    'title' => 'Planning',
    'tasks' => $planning,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
])

{{-- ON GOING --}}
@include('tasks.partials.my-status-table', [
    'title' => 'On Going',
    'tasks' => $ongoing,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
])

{{-- REVIEWED --}}
@include('tasks.partials.my-status-table', [
    'title' => 'Reviewed',
    'tasks' => $reviewed,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
])

{{-- FINISHED --}}
@include('tasks.partials.my-status-table', [
    'title' => 'Finished',
    'tasks' => $finished,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
])

{{-- CANCELED --}}
@include('tasks.partials.my-status-table', [
    'title' => 'Canceled',
    'tasks' => $canceled,
    'projects' => $projects,
    'priorities' => $priorities,
    'statuses' => $statuses,
])

@endsection
