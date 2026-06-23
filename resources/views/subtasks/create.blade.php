@extends('layouts.app')

@section('content')

<div class="flex items-end justify-between mb-6">

    <div>

        <h1 class="text-4xl font-bold">
            Create Subtask
        </h1>

        <p class="text-gray-500 mt-2">
            Add a new subtask
        </p>

    </div>

    <a href="{{ route('tasks.show', $task) }}">

        <x-button-secondary>
            Back
        </x-button-secondary>

    </a>

</div>

<x-card>

    <form
        action="{{ route('subtasks.store') }}"
        method="POST">

        @csrf

        <input
            type="hidden"
            name="id_task"
            value="{{ $task->id_task }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- TASK --}}
            <div class="md:col-span-2">

                <label class="block mb-2 font-medium">
                    Parent Task
                </label>

                <input
                    type="text"
                    value="{{ $task->nama_task }}"
                    class="w-full rounded-xl border border-border p-3 bg-gray-50"
                    readonly>

            </div>

            {{-- SUBTASK NAME --}}
            <div class="md:col-span-2">

                <label class="block mb-2 font-medium">
                    Subtask Name
                </label>

                <input
                    type="text"
                    name="subtask_name"
                    value="{{ old('subtask_name') }}"
                    class="w-full rounded-xl border border-border p-3"
                    required>

                @error('subtask_name')

                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>

            {{-- DEADLINE --}}
            <div>

                <label class="block mb-2 font-medium">
                    Deadline
                </label>

                <input
                    type="date"
                    name="subtask_deadline"
                    value="{{ old('subtask_deadline') }}"
                    class="w-full rounded-xl border border-border p-3">

                <p class="text-xs text-gray-500 mt-1">

                    Task Deadline:
                    {{ $task->deadline_task }}

                </p>

            </div>

            {{-- PRIORITY --}}
            <div>

                <label class="block mb-2 font-medium">
                    Priority
                </label>

                <select
                    name="id_priority"
                    class="w-full rounded-xl border border-border p-3">

                    <option value="">
                        Select Priority
                    </option>

                    @foreach($priorities as $priority)

                        <option
                            value="{{ $priority->id_priority }}"
                            @selected(
                                old('id_priority')
                                == $priority->id_priority
                            )>

                            {{ $priority->priority_name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- STATUS --}}
            <div>

                <label class="block mb-2 font-medium">
                    Status
                </label>

                <select
                    name="id_status"
                    class="w-full rounded-xl border border-border p-3">

                    <option value="">
                        Select Status
                    </option>

                    @foreach($statuses as $status)

                        <option
                            value="{{ $status->id_status }}"
                            @selected(
                                old('id_status')
                                == $status->id_status
                            )>

                            {{ $status->status_name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- ASSIGNEES --}}
            <div class="md:col-span-2">

                <label class="block mb-2 font-medium">
                    Assignees
                </label>

                <select
                    id="assignees"
                    name="assignees[]"
                    multiple>

                    @foreach($members as $member)

                        <option
                            value="{{ $member->id_member }}">

                            {{ $member->member_name }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- NOTE --}}
            <div class="md:col-span-2">

                <label class="block mb-2 font-medium">
                    Note
                </label>

                <textarea
                    name="note"
                    rows="5"
                    class="w-full rounded-xl border border-border p-3">{{ old('note') }}</textarea>

            </div>

        </div>

        <div class="flex justify-end mt-8">

            <x-button-primary>
                Create Subtask
            </x-button-primary>

        </div>

    </form>

</x-card>

{{-- TOMSELECT --}}
<link
    href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css"
    rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        new TomSelect(
            '#assignees',
            {
                plugins: [
                    'remove_button'
                ],

                create: false,

                placeholder:
                    'Select assignees'
            }
        );

    }
);

</script>

<style>

.ts-control {

    border-radius: 1rem !important;

    border: 1px solid #E5E7EB !important;

    min-height: 48px !important;

    padding: 10px 12px !important;

    box-shadow: none !important;

}

.ts-wrapper.focus .ts-control {

    border-color: #f97316 !important;

    box-shadow: 0 0 0 3px rgba(249,115,22,.15) !important;

}

</style>

@endsection 