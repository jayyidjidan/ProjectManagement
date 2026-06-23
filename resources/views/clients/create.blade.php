@extends('layouts.app')

@section('content')

<div class="max-w-3xl">

    <div class="mb-6 space-y-2">

        <h1 class="text-4xl font-bold">
            Clients
        </h1>

        <p class="text-gray-500">
            Add new client
        </p>

    </div>

    <x-card>

        <form
            method="POST"
            action="{{ route('clients.store') }}">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block mb-2">
                        Client Name
                    </label>

                    <x-input
                        name="nama_klien"
                        value="{{ old('nama_klien') }}" />
                </div>

                <div>
                    <label class="block mb-2">
                        Phone Number
                    </label>

                    <x-input
                        name="no_telp"
                        value="{{ old('no_telp') }}" />
                </div>

                <div>
                    <label class="block mb-2">
                        Email
                    </label>

                    <x-input
                        type="email"
                        name="email"
                        value="{{ old('email') }}" />
                </div>

                <div>
                    <label class="block mb-2">
                        Country
                    </label>

                    <x-input
                        name="asal_negara"
                        value="{{ old('asal_negara') }}" />
                </div>

                <div class="md:col-span-2">

                    <label class="block mb-2">
                        Client Source
                    </label>

                    <select
                        name="id_sumber_klien"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                        <option value="">
                            Select Source
                        </option>

                        @foreach($sources as $source)

                            <option
                                value="{{ $source->id_sumberklien }}">

                                {{ $source->nama_sumber }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="mt-6 flex gap-3">

                <x-button-primary type="submit">
                    Save
                </x-button-primary>

                <a href="{{ route('clients.index') }}">
                    <x-button-secondary>
                        Cancel
                    </x-button-secondary>
                </a>

            </div>

        </form>

    </x-card>

</div>

@endsection