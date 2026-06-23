@extends('layouts.app')

@section('content')

@if (session('success'))
    <div
        class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-end justify-between mb-6">

    <div class="space-y-2">

        <h1 class="text-4xl font-bold">
            Projects
        </h1>

        <p class="text-gray-500">
            Manage all projects in your company
        </p>

    </div>

    <a href="{{ route('projects.create') }}">
        <x-button-primary>
            Add Project
        </x-button-primary>
    </a>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

    <a
        href="{{ route('projects.index') }}"
        class="p-5 rounded-2xl border border-border bg-white transition hover:border-primary">

        <p class="text-sm text-gray-500">
            Total Projects
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $total }}
        </h2>

    </a>

    <a
        href="{{ route('projects.index', ['status' => 1]) }}"
        class="p-5 rounded-2xl border border-border bg-white transition hover:border-yellow-400">

        <p class="text-sm text-yellow-600">
            Planning
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $planning }}
        </h2>

    </a>

    <a
        href="{{ route('projects.index', ['status' => 2]) }}"
        class="p-5 rounded-2xl border border-border bg-white transition hover:border-blue-400">

        <p class="text-sm text-blue-600">
            On Going
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $ongoing }}
        </h2>

    </a>

    <a
        href="{{ route('projects.index', ['status' => 3]) }}"
        class="p-5 rounded-2xl border border-border bg-white transition hover:border-green-400">

        <p class="text-sm text-green-600">
            Finished
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $finished }}
        </h2>

    </a>

    <a
        href="{{ route('projects.index', ['status' => 4]) }}"
        class="p-5 rounded-2xl border border-border bg-white transition hover:border-red-400">

        <p class="text-sm text-red-600">
            Cancelled
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $cancelled }}
        </h2>

    </a>

</div>

<x-card>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="text-left p-4 w-20">
                        No
                    </th>

                    <th class="text-left p-4">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'nama_proyek',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}"
                            class="hover:text-primary">

                            Project Name

                        </a>

                    </th>

                    <th class="text-left p-4">
                        Categories
                    </th>

                    <th class="text-left p-4">
                        Type
                    </th>

                    <th class="text-left p-4">
                        Client
                    </th>

                    <th class="text-left p-4">
                        Project Manager
                    </th>

                    <th class="text-left p-4">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'deadline',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}"
                            class="hover:text-primary">

                            Deadline

                        </a>

                    </th>

                <th class="text-left p-4">
                    <a
                        href="{{ request()->fullUrlWithQuery([
                            'sort' => 'id_status',
                            'direction' => request('direction') == 'asc' ? 'desc' : 'asc'
                        ]) }}"
                        class="hover:text-primary">
                        Status
                    </a>
                </th>

                    <th class="text-right p-4 w-64">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($projects as $project)

                    <tr
                        onclick="window.location='{{ route('projects.show', $project) }}'"
                        class="group border-b border-border cursor-pointer hover:bg-gray-50 transition">

                        <td class="p-4">

                            {{ $projects->firstItem() + $loop->index }}

                        </td>

                        <td class="p-4 font-medium">

                            <div class="flex items-center gap-2">

                                {{ $project->nama_proyek }}

                                <span
                                    class="opacity-0 group-hover:opacity-100 transition">

                                    →

                                </span>

                            </div>

                        </td>

                        <td class="p-4 max-w-xs">

                            <div class="flex flex-wrap gap-2">

                                @forelse($project->kategoris as $category)

                                    <span
                                        class="px-3 py-1 text-xs rounded-full border border-border">

                                        {{ $category->nama_kategori }}

                                    </span>

                                @empty

                                    <span class="text-gray-400">
                                        -
                                    </span>

                                @endforelse

                            </div>

                        </td>

                        <td class="p-4">
                            {{ $project->tipe->nama_tipe ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $project->klien->nama_klien ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $project->projectManager->member_name ?? '-' }}
                        </td>

                        <td class="p-4">

                            {{ $project->deadline
                                ? \Carbon\Carbon::parse($project->deadline)->format('d M Y')
                                : '-' }}

                        </td>

                        <td class="p-4">

                            @php
                                $status = $project->status->nama_status ?? '';
                            @endphp

                            @if($status == 'Planning')

                                <span
                                    class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700">

                                    Planning

                                </span>

                            @elseif($status == 'On Going')

                                <span
                                    class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700">

                                    On Going

                                </span>

                            @elseif($status == 'Finished')

                                <span
                                    class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">

                                    Finished

                                </span>

                            @elseif($status == 'Cancelled')

                                <span
                                    class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700">

                                    Cancelled

                                </span>

                            @endif

                        </td>

                        <td class="p-4">

                            <div class="flex justify-end gap-2">

                                <a
                                    onclick="event.stopPropagation()"
                                    href="{{ route('projects.edit', $project) }}"
                                    class="px-4 py-2 rounded-xl border border-border hover:bg-gray-50">

                                    Edit

                                </a>

                                <form
                                    onclick="event.stopPropagation()"
                                    action="{{ route('projects.destroy', $project) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this project?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50">

                                        Delete

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="text-center py-16 text-gray-500">

                            No Projects Available

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-card>

<div class="mt-6">

    {{ $projects->links() }}

</div>

@endsection