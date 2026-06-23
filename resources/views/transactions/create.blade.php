@extends('layouts.app')

@section('content')

<div class="max-w-4xl">

    <div class="mb-6 space-y-2">

        <h1 class="text-4xl font-bold">
            Transactions
        </h1>

        <p class="text-gray-500">
            Add new transaction
        </p>

    </div>

    <x-card>

        <form
            method="POST"
            action="{{ route('transactions.store') }}"
            enctype="multipart/form-data">

            @csrf

            <input
                type="hidden"
                name="id_pembayaran"
                value="{{ $payment->id_pembayaran }}">



            <div class="space-y-6">

                <div>

                    <label class="block mb-2">
                        Project
                    </label>

                    <input
                        type="text"
                        readonly
                        value="{{ $payment->project->nama_proyek ?? '-' }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border bg-gray-50">

                </div>

                <div>

                    <label class="block mb-2">
                        Transaction Date
                    </label>

                    <input
                        type="date"
                        name="tanggal_transaksi"
                        value="{{ old('tanggal_transaksi', date('Y-m-d')) }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                </div>

                <div>

                    <label class="block mb-2">
                        Amount
                    </label>

                    <input
                        type="number"
                        min="1"
                        name="jumlah_transaksi"
                        value="{{ old('jumlah_transaksi') }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border">
                    
                        @error('jumlah_transaksi')

                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>

                        @enderror

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
                                value="{{ $type->id_jenis }}">

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

                        <option value="">
                            Select Method
                        </option>

                        <option value="Transfer">
                            Transfer
                        </option>

                        <option value="Cash">
                            Cash
                        </option>

                        <option value="QRIS">
                            QRIS
                        </option>

                    </select>

                </div>

                <div>

                    <label class="block mb-2">
                        Proof of Payment
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
                        class="w-full px-4 py-3 rounded-2xl border border-border">{{ old('note') }}</textarea>

                </div>

            </div>

            <div class="mt-8 flex gap-3">

                <x-button-primary type="submit">

                    Save

                </x-button-primary>

                <a href="{{ route('payments.index') }}">

                    <x-button-secondary>

                        Cancel

                    </x-button-secondary>

                </a>

            </div>

        </form>

    </x-card>

</div>

@endsection