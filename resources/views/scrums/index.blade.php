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
            Scrum
        </h1>

        <p class="text-gray-500 mt-2">
            Daily Scrum Management
        </p>

    </div>

    <a href="{{ route('scrum.today') }}">

        <x-button-primary>
            Open Today's Scrum
        </x-button-primary>

    </a>

</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <x-card>

        <p class="text-gray-500 text-sm">
            Total Scrum
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $scrums->total() }}
        </h2>

    </x-card>

    <x-card>

        <p class="text-gray-500 text-sm">
            Finalized
        </p>

        <h2 class="text-3xl font-bold text-green-600 mt-2">

            {{ $scrums->where('is_locked', 1)->count() }}

        </h2>

    </x-card>

    <x-card>

        <p class="text-gray-500 text-sm">
            Draft
        </p>

        <h2 class="text-3xl font-bold text-yellow-600 mt-2">

            {{ $scrums->where('is_locked', 0)->count() }}

        </h2>

    </x-card>

</div>

<x-card>

    <div class="flex items-center justify-between mb-6">

        <h2 class="text-xl font-semibold">

            Scrum History

        </h2>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="p-4 text-left">
                        Date
                    </th>

                    <th class="p-4 text-left">
                        Day
                    </th>

                    <th class="p-4 text-left">
                        Responsible
                    </th>

                    <th class="p-4 text-left">
                        Status
                    </th>

                    <th class="p-4 text-right">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($scrums as $scrum)

                    <tr class="border-b border-border">

                        <td class="p-4">

                            {{ \Carbon\Carbon::parse($scrum->date_scrum)->format('d M Y') }}

                        </td>

                        <td class="p-4">

                            {{ $scrum->day }}

                        </td>

                        <td class="p-4">

                            {{ $scrum->responsible?->member_name ?? '-' }}

                        </td>

                        <td class="p-4">

                            @if($scrum->is_locked)

                                <span
                                    class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs">

                                    Finalized

                                </span>

                            @else

                                <span
                                    class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs">

                                    Draft

                                </span>

                            @endif

                        </td>

                        <td class="p-4 text-right">

                            <a
                                href="{{ route('scrums.show', $scrum) }}"
                                class="text-primary hover:underline">

                                View

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center py-10 text-gray-500">

                            No Scrum History

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-6">

        {{ $scrums->links() }}

    </div>

</x-card>

@endsection