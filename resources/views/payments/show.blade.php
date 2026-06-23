@extends('layouts.app')

@section('content')

@if (session('success'))
    <div
        class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-end justify-between mb-6">

    <div>

        <h1 class="text-4xl font-bold">
            Payment Detail
        </h1>

        <p class="text-gray-500 mt-2">
            View payment information and transaction history
        </p>

    </div>

    <a
        href="{{ route('transactions.create', [
            'payment' => $payment->id_pembayaran
        ]) }}">

        <x-button-primary>
            Add Transaction
        </x-button-primary>

    </a>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

    <x-card>

        <p class="text-sm text-gray-500">
            Project
        </p>

        <h3 class="font-semibold mt-2">
            {{ $payment->project->nama_proyek ?? '-' }}
        </h3>

    </x-card>

    <x-card>

        <p class="text-sm text-gray-500">
            Total Payment
        </p>

        <h3 class="font-semibold mt-2">
            Rp {{ number_format($payment->total_pembayaran, 0, ',', '.') }}
        </h3>

    </x-card>

    <x-card>

        <p class="text-sm text-gray-500">
            Paid
        </p>

        <h3 class="font-semibold text-green-600 mt-2">
            Rp {{ number_format($payment->total_dibayarkan, 0, ',', '.') }}
        </h3>

    </x-card>

    <x-card>

        <p class="text-sm text-gray-500">
            Remaining
        </p>

        <h3 class="font-semibold text-red-600 mt-2">
            Rp {{ number_format($payment->sisa_pembayaran, 0, ',', '.') }}
        </h3>

    </x-card>

    <x-card>

        <p class="text-sm text-gray-500">
            Status
        </p>

        @if($payment->sisa_pembayaran <= 0)

            <span
                class="inline-flex mt-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">

                Finished

            </span>

        @else

            <span
                class="inline-flex mt-2 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm">

                Remaining

            </span>

        @endif

    </x-card>

</div>

<x-card>

    <div class="flex items-center justify-between mb-6">

        <h2 class="text-xl font-semibold">
            Transaction History
        </h2>

        <span class="text-sm text-gray-500">

            {{ $payment->transaksis->count() }}
            Transactions

        </span>

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

                    <th class="p-4 text-right">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($payment->transaksis as $transaction)

                    <tr class="border-b border-border">

                        <td class="p-4">

                            {{ \Carbon\Carbon::parse(
                                $transaction->tanggal_transaksi
                            )->format('d M Y') }}

                        </td>

                        <td class="p-4">

                            {{ $transaction->jenis->nama_jenis ?? '-' }}

                        </td>

                        <td class="p-4 font-medium text-green-600">

                            Rp {{ number_format(
                                $transaction->jumlah_transaksi,
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>

                        <td class="p-4">

                            {{ $transaction->metode_pembayaran ?? '-' }}

                        </td>

                        <td class="p-4">

                            @if($transaction->bukti_transaksi)

                                <a
                                    href="{{ asset('storage/' . $transaction->bukti_transaksi) }}"
                                    target="_blank"
                                    class="text-primary hover:underline">

                                    View Proof

                                </a>

                            @else

                                -

                            @endif

                        </td>

                        <td class="p-4">

                            <div class="flex justify-end gap-2">

                                <a
                                    href="{{ route('transactions.edit', $transaction) }}"
                                    class="px-4 py-2 rounded-xl border border-border hover:bg-gray-50">

                                    Edit

                                </a>

                                <form
                                    action="{{ route('transactions.destroy', $transaction) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this transaction?')">

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
                            colspan="6"
                            class="text-center py-12 text-gray-500">

                            No Transactions Available

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-card>

@endsection