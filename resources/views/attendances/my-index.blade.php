@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto">

    <x-card>

        <h1 class="text-3xl font-bold">

            My Attendance

        </h1>

    </x-card>

    <form
        method="GET"
        class="mt-6">

        <input
            type="date"
            name="tanggal"
            value="{{ request('tanggal') }}"
            class="border rounded-xl p-3">

        <button
            class="px-4 py-3 bg-black text-white rounded-xl">

            Filter

        </button>

    </form>

    <x-card class="mt-6">

        <table class="w-full">

            <thead>

            <tr>

                <th>Date</th>
                <th>Status</th>
                <th>Check In</th>
                <th>Check Out</th>

            </tr>

            </thead>

            <tbody>

            @foreach($attendances as $attendance)

            <tr>

                <td>

                    {{ $attendance->tanggal }}

                </td>

                <td>

                    {{ $attendance->status?->status_name }}

                </td>

                <td>

                    {{ $attendance->start_hour ?? '-' }}

                </td>

                <td>

                    {{ $attendance->leave_hour ?? '-' }}

                </td>

            </tr>

            @endforeach

            </tbody>

        </table>

    </x-card>

    <div class="mt-4">

        {{ $attendances->links() }}

    </div>

</div>

@endsection