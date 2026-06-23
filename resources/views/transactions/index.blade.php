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
            Transactions
        </h1>

        <p class="text-gray-500">
            Manage all transactions in your company
        </p>

    </div>

</div>

<x-card class="mb-6">

    <form
        method="GET"
        class="flex flex-wrap gap-4 items-end">

        <div>

            <label class="block mb-2">
                Transaction Type
            </label>

            <select
                name="id_jenis"
                class="h-12 px-4 rounded-2xl border border-border">

                <option value="">
                    All Types
                </option>

                @foreach($types as $type)

                    <option
                        value="{{ $type->id_jenis }}"
                        @selected(request('id_jenis') == $type->id_jenis)>

                        {{ $type->nama_jenis }}

                    </option>

                @endforeach

            </select>

        </div>

        <div>

            <label class="block mb-2">
                Payment Method
            </label>

            <select
                name="payment_method"
                class="h-12 px-4 rounded-2xl border border-border">

                <option value="">
                    All Methods
                </option>

                <option
                    value="Transfer"
                    @selected(request('payment_method') == 'Transfer')>

                    Transfer

                </option>

                <option
                    value="Cash"
                    @selected(request('payment_method') == 'Cash')>

                    Cash

                </option>

                <option
                    value="QRIS"
                    @selected(request('payment_method') == 'QRIS')>

                    QRIS

                </option>

            </select>

        </div>

        <x-button-primary type="submit">
            Filter
        </x-button-primary>

    </form>

</x-card>

<x-card>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="p-4 text-left w-20">
                        No
                    </th>

                    <th class="p-4 text-left">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'tanggal_transaksi',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}">

                            Date

                        </a>

                    </th>

                    <th class="p-4 text-left">
                        Project
                    </th>

                    <th class="p-4 text-left">
                        Transaction Type
                    </th>

                    <th class="p-4 text-left">

                        <a
                            href="{{ request()->fullUrlWithQuery([
                                'sort' => 'jumlah_transaksi',
                                'direction' => request('direction') == 'asc'
                                    ? 'desc'
                                    : 'asc'
                            ]) }}">

                            Amount

                        </a>

                    </th>

                    <th class="p-4 text-left">
                        Payment Method
                    </th>

                    <th class="p-4 text-left">
                        Note
                    </th>

                    <th class="p-4 text-left">
                        Proof
                    </th>

                    <th class="p-4 text-right w-48">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($transactions as $transaction)

                    <tr class="border-b border-border">

                        <td class="p-4">

                            {{ $transactions->firstItem() + $loop->index }}

                        </td>

                        <td class="p-4">

                            {{ \Carbon\Carbon::parse(
                                $transaction->tanggal_transaksi
                            )->format('d M Y') }}

                        </td>

                        <td class="p-4">

                            {{ $transaction->pembayaran->project->nama_proyek ?? '-' }}

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

                            {{ $transaction->note ?? '-' }}

                        </td>

                        <td class="p-4">

                            @if($transaction->bukti_transaksi)

                                <a
                                    href="{{ asset('storage/' . $transaction->bukti_transaksi) }}"
                                    target="_blank"
                                    class="text-primary">

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
                            colspan="9"
                            class="text-center py-16 text-gray-500">

                            No Transactions Available

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-card>

<div class="mt-6">

    {{ $transactions->links() }}

</div>

@endsection