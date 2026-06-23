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
            Employees
        </h1>

        <p class="text-gray-500 mt-2">
            Manage all employees
        </p>

    </div>

    <a
        href="{{ route('invitations.create') }}">

        <x-button-primary>
            Invite Employee
        </x-button-primary>

    </a>

</div>

<x-card>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="p-4 text-left">
                        Employee
                    </th>

                    <th class="p-4 text-left">
                        Position
                    </th>

                    <th class="p-4 text-left">
                        Role
                    </th>

                    <th class="p-4 text-left">
                        Joined Date
                    </th>

                    <th class="p-4 text-right">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($members as $member)

                <tr
                    onclick="window.location='{{ route('members.show',$member) }}'"
                    class="cursor-pointer hover:bg-gray-50 border-b border-border">

                    <td class="p-4">

                        <div>

                            <p class="font-medium">

                                {{ $member->member_name }}

                            </p>

                            <p class="text-xs text-gray-500">

                                {{ $member->user?->email }}

                            </p>

                        </div>

                    </td>

                    <td class="p-4">

                        {{ $member->jabatan?->position_name ?? '-' }}

                    </td>

                    <td class="p-4">

                        {{ $member->user?->role?->role_name ?? '-' }}

                    </td>

                    <td class="p-4">

                        {{ $member->joined_date
                            ? \Carbon\Carbon::parse(
                                $member->joined_date
                              )->format('d M Y')
                            : '-'
                        }}

                    </td>

                    <td
                        class="p-4 text-right"
                        onclick="event.stopPropagation()">

                        <form
                            action="{{ route('members.destroy',$member) }}"
                            method="POST"
                            onsubmit="return confirm('Delete employee?')">

                            @csrf
                            @method('DELETE')

                            <button
                                class="text-red-600 hover:text-red-700">

                                Delete

                            </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="9"
                        class="text-center py-10 text-gray-500">

                        No Employees Found

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-6">

        {{ $members->links() }}

    </div>

</x-card>

@endsection