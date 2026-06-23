@extends('layouts.app')

@section('content')

@if(session('success'))

<div
    class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">

    {{ session('success') }}

</div>

@endif

@if($scrum->is_locked)

<div
    class="mb-6 p-4 rounded-2xl border border-blue-200 bg-blue-50 text-blue-700">

    Scrum has been finalized and can no longer be edited.

</div>

@endif

<div class="flex items-end justify-between mb-6">

<div>

    <h1 class="text-4xl font-bold">
        Daily Scrum
    </h1>

    <p class="text-gray-500 mt-2">

        {{ \Carbon\Carbon::parse($scrum->date_scrum)->format('l, d F Y') }}

    </p>

</div>

<a href="{{ route('scrums.index') }}">

    <x-button-secondary>
        Back
    </x-button-secondary>

</a>

</div>

<x-card class="mb-6">

<div class="grid grid-cols-2 gap-6">

    <div>

        <p class="text-sm text-gray-500">
            Scrum Date
        </p>

        <p class="font-medium">

            {{ \Carbon\Carbon::parse($scrum->date_scrum)->format('d M Y') }}

        </p>

    </div>

    <div>

        <p class="text-sm text-gray-500">
            Responsible
        </p>

        <p class="font-medium">

            {{ $scrum->responsible?->member_name ?? '-' }}

        </p>

    </div>

</div>

</x-card>

<form
    action="{{ route('scrums.save', $scrum) }}"
    method="POST">

@csrf

<x-card>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="p-4 text-left">
                        Name
                    </th>

                    <th class="p-4 text-left">
                        Status
                    </th>

                    <th class="p-4 text-left">
                        Task 1
                    </th>

                    <th class="p-4 text-left">
                        Target 1
                    </th>

                    <th class="p-4 text-left">
                        Task 2
                    </th>

                    <th class="p-4 text-left">
                        Target 2
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($members as $member)

                    @php

                        $update =
                            $scrum->updates
                                ->where(
                                    'id_member',
                                    $member->id_member
                                )
                                ->first();

                        $scrumMember =
                            $scrum->members
                                ->where(
                                    'id_member',
                                    $member->id_member
                                )
                                ->first();

                        $memberTasks =
                            $tasks->filter(
                                function ($task) use ($member)
                                {
                                    return $task->assignees
                                        ->contains(
                                            'id_member',
                                            $member->id_member
                                        );
                                }
                            );

                    @endphp

                    <tr
                        class="border-b border-border">

                        <td class="p-4">

                            {{ $member->member_name }}

                        </td>

                        <td class="p-4">

                            <select
                                name="members[{{ $member->id_member }}][id_status]" @disabled($scrum->is_locked)
                                class="w-full rounded-xl border border-border p-2">

                                @foreach($statuses as $status)

                                    <option
                                        value="{{ $status->id_status }}"
                                        @selected(
                                            optional($scrumMember)->pivot?->id_status
                                            ==
                                            $status->id_status
                                        )>

                                        {{ $status->status_name }}

                                    </option>

                                @endforeach

                            </select>

                        </td>   

                        <td class="p-4">

                            <select
                                name="members[{{ $member->id_member }}][id_task_1]"
                                class="w-full rounded-xl border border-border p-2">

                                <option value="">
                                    -
                                </option>

                                @foreach($memberTasks as $task)

                                    <option
                                        value="{{ $task->id_task }}"
                                        @selected(
                                            optional($update)->id_task_1
                                            ==
                                            $task->id_task
                                        )>

                                        {{ $task->nama_task }}

                                    </option>

                                @endforeach

                            </select>

                        </td>

                        <td class="p-4">

                            <input
                                type="text"
                                name="members[{{ $member->id_member }}][target_1]"
                                value="{{ $update->target_1 ?? '' }}"
                                class="w-full rounded-xl border border-border p-2">

                        </td>

                        <td class="p-4">

                            <select
                                name="members[{{ $member->id_member }}][id_task_2]"
                                class="w-full rounded-xl border border-border p-2">

                                <option value="">
                                    -
                                </option>

                                @foreach($memberTasks as $task)

                                    <option
                                        value="{{ $task->id_task }}"
                                        @selected(
                                            optional($update)->id_task_2
                                            ==
                                            $task->id_task
                                        )>

                                        {{ $task->nama_task }}

                                    </option>

                                @endforeach

                            </select>

                        </td>

                        <td class="p-4">

                            <input
                                type="text"
                                name="members[{{ $member->id_member }}][target_2]"
                                value="{{ $update->target_2 ?? '' }}"
                                class="w-full rounded-xl border border-border p-2">

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    @if(!$scrum->is_locked)

    <div class="flex justify-end mt-6">

        <x-button-primary>
            Save Scrum
        </x-button-primary>

    </div>

    @endif

</x-card>

</form>

@endsection
