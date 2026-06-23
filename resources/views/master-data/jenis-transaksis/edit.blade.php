@extends('layouts.app')

@section('content')

<div class="max-w-2xl">

    <div class="mb-6 space-y-2">

        <h1 class="text-4xl font-bold">
            Transaction Types
        </h1>

        <p class="text-gray-500">
            Edit transaction types in your company
        </p>

    </div>

    <x-card>

        <form
            method="POST"
            action="{{ route('jenis-transaksis.update', $jenis_transaksi) }}">

            @csrf
            @method('PUT')

            <div class="space-y-4">

                <div>

                    <label
                        for="nama_jenis"
                        class="block mb-2">

                        Transaction Type

                    </label>

                    <x-input
                        id="nama_jenis"
                        name="nama_jenis"
                        value="{{ old('nama_jenis', $jenis_transaksi->nama_jenis) }}" />

                </div>

            </div>

            <div class="mt-6 flex gap-3">

                <x-button-primary type="submit">
                    Save
                </x-button-primary>

                <a href="{{ route('jenis-transaksis.index') }}">
                    <x-button-secondary>
                        Cancel
                    </x-button-secondary>
                </a>

            </div>

        </form>

    </x-card>

</div>

@endsection