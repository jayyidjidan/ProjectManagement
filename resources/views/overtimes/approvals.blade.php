@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    <x-card>

        <h1 class="text-3xl font-bold mb-2">

            Overtime Approvals

        </h1>

        <p class="text-gray-500">

            Pending overtime requests

        </p>

    </x-card>

    @if(session('success'))

    <div class="mt-4 p-4 rounded-xl bg-green-100 text-green-700">

        {{ session('success') }}

    </div>

    @endif

    <x-card class="mt-6">

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="text-left py-3">

                        Employee

                    </th>

                    <th class="text-left py-3">

                        Task

                    </th>

                    <th class="text-left py-3">

                        Duration

                    </th>

                    <th class="text-left py-3">

                        Reason

                    </th>

                    <th class="text-left py-3">

                        Action

                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($overtimes as $overtime)

                <tr class="border-b">

                    <td class="py-3">

                        {{ $overtime->member->member_name }}

                    </td>

                    <td class="py-3">

                        {{ $overtime->task?->nama_task }}

                    </td>

                    <td class="py-3">

                        {{ $overtime->durasi_jam }}

                        Hours

                    </td>

                    <td class="py-3">

                        {{ $overtime->reason }}

                    </td>

                    <td class="py-3">

                        <div class="flex gap-2">

                            <form
                                action="{{ route('overtimes.approve',$overtime->id_riwayat) }}"
                                method="POST">

                                @csrf

                                <button
                                    class="px-4 py-2 rounded-lg bg-green-600 text-white">

                                    Approve

                                </button>

                            </form>

                            <form
                                action="{{ route('overtimes.reject',$overtime->id_riwayat) }}"
                                method="POST">

                                @csrf

                                <button
                                    class="px-4 py-2 rounded-lg bg-red-600 text-white">

                                    Reject

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="5"
                        class="text-center py-8">

                        No pending overtime

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mt-6">

            {{ $overtimes->links() }}

        </div>

    </x-card>

</div>

@endsection