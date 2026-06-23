@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    <x-card>

        <h1 class="text-3xl font-bold">

            My Overtime

        </h1>

    </x-card>

    <x-card class="mt-6">

        <table class="w-full">

            <thead>

            <tr>

                <th>Date</th>
                <th>Task</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Approved At</th>

            </tr>

            </thead>

            <tbody>

            @foreach($histories as $item)

            <tr>

                <td>

                    {{ $item->tanggal }}

                </td>

                <td>

                    {{ $item->task?->nama_task }}

                </td>

                <td>

                    {{ $item->durasi_jam }}

                    Hour

                </td>

                <td>

                    @if($item->status_approval == 'approved')

                        <span class="text-green-600">

                            Approved

                        </span>

                    @elseif($item->status_approval == 'rejected')

                        <span class="text-red-600">

                            Rejected

                        </span>

                    @else

                        <span class="text-yellow-600">

                            Pending

                        </span>

                    @endif

                </td>

                <td>

                    {{ $item->approved_at ?? '-' }}

                </td>

            </tr>

            @endforeach

            </tbody>

        </table>

    </x-card>

    <div class="mt-4">

        {{ $histories->links() }}

    </div>

</div>

@endsection