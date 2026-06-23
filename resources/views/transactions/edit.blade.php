@extends('layouts.app')

@section('content')

<div class="max-w-4xl">

    <div class="mb-6 space-y-2">

        <h1 class="text-4xl font-bold">
            Transactions
        </h1>

        <p class="text-gray-500">
            Edit transaction
        </p>

    </div>

    <x-card>

        <form
            method="POST"
            action="{{ route('transactions.update', $transaction) }}"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="space-y-6">

                <div>

                    <label class="block mb-2">
                        Transaction Date
                    </label>

                    <input
                        type="date"
                        name="tanggal_transaksi"
                        value="{{ old('tanggal_transaksi', $transaction->tanggal_transaksi) }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                    @error('jumlah_transaksi')

                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>

                    @enderror

                </div>

                <div>

                    <label class="block mb-2">
                        Amount
                    </label>

                    <input
                        type="number"
                        min="1"
                        name="jumlah_transaksi"
                        value="{{ old('jumlah_transaksi', $transaction->jumlah_transaksi) }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                </div>

                <div>

                    <label class="block mb-2">
                        Transaction Type
                    </label>

                    <select
                        name="id_jenis"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                        <option value="">
                            Select Type
                        </option>

                        @foreach($types as $type)

                            <option
                                value="{{ $type->id_jenis }}"
                                @selected($transaction->id_jenis == $type->id_jenis)>

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
                        name="metode_pembayaran"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                        <option value="Transfer"
                            @selected($transaction->metode_pembayaran == 'Transfer')>
                            Transfer
                        </option>

                        <option value="Cash"
                            @selected($transaction->metode_pembayaran == 'Cash')>
                            Cash
                        </option>

                        <option value="QRIS"
                            @selected($transaction->metode_pembayaran == 'QRIS')>
                            QRIS
                        </option>

                    </select>

                </div>

                <div>

                    <label class="block mb-2">
                        Current Proof
                    </label>

                    @if($transaction->bukti_transaksi)

                        <a
                            href="{{ asset('storage/' . $transaction->bukti_transaksi) }}"
                            target="_blank"
                            class="text-primary">

                            View Current File

                        </a>

                    @else

                        <p>-</p>

                    @endif

                </div>

                <div>

                    <label class="block mb-2">
                        New Proof
                    </label>

                    <input
                        type="file"
                        name="bukti_transaksi"
                        class="w-full p-3 rounded-2xl border border-border">

                </div>

                <div>

                    <label class="block mb-2">
                        Note
                    </label>

                    <textarea
                        name="note"
                        rows="4"
                        class="w-full px-4 py-3 rounded-2xl border border-border">{{ old('note', $transaction->note) }}</textarea>

                </div>

            </div>

            <div class="mt-8 flex gap-3">

                <x-button-primary type="submit">

                    Save Changes

                </x-button-primary>

                <a href="{{ route('transactions.index') }}">

                    <x-button-secondary>

                        Cancel

                    </x-button-secondary>

                </a>

            </div>

        </form>

    </x-card>

</div>

@endsection