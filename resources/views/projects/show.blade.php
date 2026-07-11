    @extends('layouts.app')

    @section('content')

    <div class="flex items-end justify-between mb-6">

        <div class="flex items-start gap-3 mt-2">
            <x-button-secondary>
                <a href="{{ route('projects.index') }}"  class="text-sm text-gray-500">Back</a>
            </x-button-secondary>

            <div>
                <h1 class="text-4xl font-bold">
                    {{ $project->nama_proyek }}
                </h1>

                

                <p class="text-gray-500">
                    Project Detail Information
                </p>
            </div>
        </div>

        <a href="{{ route('projects.edit', $project) }}">

            <x-button-primary>
                Edit Project
            </x-button-primary>

        </a>

    </div>

    <div class="flex gap-6">

        {{-- PROJECT INFORMATION --}}
        <x-card class="flex-1">

            <h2 class="text-xl font-semibold mb-4">
                Project Information
            </h2>

            <div class="space-y-4">

                <div>
                    <p class="text-sm text-gray-500">
                        Project Name
                    </p>

                    <p>
                        {{ $project->nama_proyek }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <p>
                        {{ $project->status->nama_status ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Type
                    </p>

                    <p>
                        {{ $project->tipe->nama_tipe ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Deadline
                    </p>

                    <p>
                        {{ $project->deadline }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Project Manager
                    </p>

                    <p>
                        {{ $project->projectManager->member_name ?? '-' }}
                    </p>
                </div>

                <div>

                    <p class="text-sm text-gray-500 mb-2">
                        Categories
                    </p>

                    <div class="flex flex-wrap gap-2">

                        @foreach($project->kategoris as $category)

                            <span
                                class="px-3 py-1 rounded-full border border-border text-sm">

                                {{ $category->nama_kategori }}

                            </span>

                        @endforeach

                    </div>

                </div>

            </div>

        </x-card>

        {{-- CLIENT INFORMATION --}}
        <x-card class="flex-1">

            <h2 class="text-xl font-semibold mb-4">
                Client Information
            </h2>

            <div class="space-y-4">

                <div>
                    <p class="text-sm text-gray-500">
                        Client Name
                    </p>

                    <p>
                        {{ $project->klien->nama_klien ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Email
                    </p>

                    <p>
                        {{ $project->klien->email ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Phone
                    </p>

                    <p>
                        {{ $project->klien->no_telp ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Country
                    </p>

                    <p>
                        {{ $project->klien->asal_negara ?? '-' }}
                    </p>
                </div>

            </div>

        </x-card>

        {{-- PAYMENT INFORMATION --}}
        <x-card class="flex-1">

            <div class="flex items-center justify-between mb-4">

                <h2 class="text-xl font-semibold">
                    Payment Information
                </h2>

                @if($project->pembayaran)

                    <a
                        href="{{ route('payments.show', $project->pembayaran) }}"
                        class="text-sm text-primary">

                        View Detail

                    </a>

                @endif

            </div>

            <div class="space-y-4">

                <div>

                    <p class="text-sm text-gray-500">
                        Total Payment
                    </p>

                    <p>
                        Rp {{ number_format($project->pembayaran->total_pembayaran ?? 0,0,',','.') }}
                    </p>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Paid
                    </p>

                    <p class="text-green-600">
                        Rp {{ number_format($project->pembayaran->total_dibayarkan ?? 0,0,',','.') }}
                    </p>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Remaining
                    </p>

                    <p class="text-red-600">
                        Rp {{ number_format($project->pembayaran->sisa_pembayaran ?? 0,0,',','.') }}
                    </p>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Installments
                    </p>

                    <p>
                        {{ $project->pembayaran->jumlah_pembayaran ?? 0 }}
                    </p>

                </div>

            </div>

        </x-card>

    </div>

    {{-- TRANSACTION HISTORY --}}
    <x-card class="mt-6">

        <div class="flex items-center justify-between mb-6">

            <h2 class="text-xl font-semibold">
                Transaction History
            </h2>

            @if($project->pembayaran)

                <a
                    href="{{ route('transactions.create', [
                        'payment' => $project->pembayaran->id_pembayaran
                    ]) }}">

                    <x-button-primary>
                        Add Transaction
                    </x-button-primary>

                </a>

            @endif

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b border-border">

                        <th class="p-4 text-left">
                            Date
                        </th>

                        <th class="p-4 text-left">
                            Type
                        </th>

                        <th class="p-4 text-left">
                            Amount
                        </th>

                        <th class="p-4 text-left">
                            Method
                        </th>

                        <th class="p-4 text-left">
                            Proof
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($project->pembayaran?->transaksis ?? [] as $transaction)

                        <tr class="border-b border-border">

                            <td class="p-4">
                                {{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d M Y') }}
                            </td>

                            <td class="p-4">
                                {{ $transaction->jenis->nama_jenis ?? '-' }}
                            </td>

                            <td class="p-4 text-green-600">
                                Rp {{ number_format($transaction->jumlah_transaksi,0,',','.') }}
                            </td>

                            <td class="p-4">
                                {{ $transaction->metode_pembayaran }}
                            </td>

                            <td class="p-4">

                                @if($transaction->bukti_transaksi)

                                    <a
                                        href="{{ asset('storage/' . $transaction->bukti_transaksi) }}"
                                        target="_blank">

                                        View Proof

                                    </a>

                                @else

                                    -

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-10 text-gray-500">

                                No Transactions Available

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </x-card>

{{-- TASK LIST --}}
    <x-card class="mt-6">

        <div class="flex items-center justify-between mb-6">

            <h2 class="text-xl font-semibold">
                Project Tasks
            </h2>

           <div class="flex items-center gap-3">
                {{-- TOMBOL VIEW ALL --}}
                <a href="{{ route('tasks.index', ['project' => $project->id_proyek]) }}">
                    <x-button-secondary>
                        View All
                    </x-button-secondary>

                </a>

                {{-- TOMBOL ADD TASK DI PROJECT SHOW --}}
                <a href="{{ route('tasks.create', [
                    'project_id' => $project->id_proyek,
                    'origin' => 'project' // Tambahkan ini
                ]) }}">
                    <x-button-primary>
                        Add Task
                    </x-button-primary>
                </a>
            </div>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b border-border">

                        <th class="p-4 text-left">
                            Task Name
                        </th>

                        <th class="p-4 text-left">
                            Assignee
                        </th>

                        <th class="p-4 text-left">
                            Priority
                        </th>

                        <th class="p-4 text-left">
                            Status
                        </th>

                        <th class="p-4 text-left">
                            Deadline
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($project->tasks as $task)

                        <tr
                            onclick="window.location='{{ route('tasks.show', $task) }}'"
                            class="cursor-pointer hover:bg-gray-50 transition border-b border-border">

                            <td class="p-4 font-medium">

                                {{ $task->nama_task }}

                            </td>

                            <td class="p-4">

                                <div class="flex flex-wrap gap-2">

                                    @forelse($task->assignees as $member)

                                        <span
                                            class="px-3 py-1 text-xs rounded-full border border-border">

                                            {{ $member->member_name }}

                                        </span>

                                    @empty

                                        <span class="text-gray-400">
                                            -

                                        </span>

                                    @endforelse

                                </div>

                            </td>

                            <td class="p-4">

                                {{ $task->priority->priority_name ?? '-' }}

                            </td>

                            <td class="p-4">

                                @php
                                    $status =
                                        $task->status->status_name ?? '';
                                @endphp

                                @if($status == 'Planning')

                                    <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700">
                                        Planning
                                    </span>

                                @elseif($status == 'On Going')

                                    <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700">
                                        On Going
                                    </span>

                                @elseif($status == 'Reviewed')

                                    <span class="px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-700">
                                        Reviewed
                                    </span>

                                @elseif($status == 'Finished')

                                    <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">
                                        Finished
                                    </span>

                                @elseif($status == 'Canceled')

                                    <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700">
                                        Canceled
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                        -
                                    </span>

                                @endif

                            </td>

                            <td class="p-4">

                                {{ $task->deadline_task
                                    ? \Carbon\Carbon::parse($task->deadline_task)->format('d M Y')
                                    : '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-10 text-gray-500">

                                No Tasks Available

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </x-card>
    @endsection