@extends('layouts.app')

@section('content')

<div class="max-w-4xl">

    <div class="mb-6 space-y-2">

        <h1 class="text-4xl font-bold">
            Projects
        </h1>

        <p class="text-gray-500">
            Edit project information
        </p>

    </div>

    <x-card>

        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('projects.update', $project) }}">

            @csrf
            @method('PUT')

            <div class="space-y-6">

                {{-- Project Name --}}
                <div>

                    <label
                        for="nama_proyek"
                        class="block mb-2">

                        Project Name

                    </label>

                    <x-input
                        id="nama_proyek"
                        name="nama_proyek"
                        value="{{ old('nama_proyek', $project->nama_proyek) }}" />

                    @error('nama_proyek')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Deadline --}}
                <div>

                    <label
                        for="deadline"
                        class="block mb-2">

                        Deadline

                    </label>

                    <input
                        type="date"
                        id="deadline"
                        name="deadline"
                        value="{{ old('deadline', $project->deadline) }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                </div>

                {{-- Status --}}
                <div>

                    <label
                        for="id_status"
                        class="block mb-2">

                        Project Status

                    </label>

                    <select
                        id="id_status"
                        name="id_status"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                        <option value="">
                            Select Status
                        </option>

                        @foreach($statuses as $status)

                            <option
                                value="{{ $status->id_status }}"
                                @selected(old('id_status', $project->id_status) == $status->id_status)>

                                {{ $status->nama_status }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Type --}}
                <div>

                    <label
                        for="id_tipe"
                        class="block mb-2">

                        Project Type

                    </label>

                    <select
                        id="id_tipe"
                        name="id_tipe"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                        <option value="">
                            Select Type
                        </option>

                        @foreach($types as $type)

                            <option
                                value="{{ $type->id_tipe }}"
                                @selected(old('id_tipe', $project->id_tipe) == $type->id_tipe)>

                                {{ $type->nama_tipe }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Client --}}
                <div>

                    <label
                        for="nama_klien"
                        class="block mb-2">

                        Client

                    </label>

                    <input
                        list="clients"
                        id="nama_klien"
                        name="nama_klien"
                        value="{{ old('nama_klien', $project->klien->nama_klien ?? '') }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                    <datalist id="clients">

                        @foreach($clients as $client)

                            <option value="{{ $client->nama_klien }}">

                        @endforeach

                    </datalist>

                </div>

                {{-- Total Payment --}}
                <div>

                    <label
                        for="total_pembayaran"
                        class="block mb-2">

                        Total Payment

                    </label>

                    <input
                        type="number"
                        min="0"
                        id="total_pembayaran"
                        name="total_pembayaran"
                        value="{{ old('total_pembayaran', $project->pembayaran->total_pembayaran ?? 0) }}"
                        class="w-full h-12 px-4 rounded-2xl border border-border">

                </div>

                {{-- Categories --}}
                <div>

                    <label class="block mb-3">

                        Categories

                    </label>

                    <div class="flex flex-wrap gap-3">

                        @foreach($categories as $category)

                            <label
                                class="px-4 py-2 rounded-2xl border border-border cursor-pointer hover:border-primary">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="{{ $category->id_kategori }}"
                                    class="mr-2"

                                    @checked(
                                        old(
                                            'categories',
                                            $project->kategoris
                                                ->pluck('id_kategori')
                                                ->toArray()
                                        )
                                        &&
                                        in_array(
                                            $category->id_kategori,
                                            old(
                                                'categories',
                                                $project->kategoris
                                                    ->pluck('id_kategori')
                                                    ->toArray()
                                            )
                                        )
                                    )>

                                {{ $category->nama_kategori }}

                            </label>

                        @endforeach

                    </div>

                </div>

            </div>

            <div class="mt-8 flex gap-3">

                <x-button-primary
                    type="submit">

                    Save Changes

                </x-button-primary>

                <x-button-secondary type="button" onclick="window.location.href='{{ route('projects.index') }}'">
                    Cancel
                </x-button-secondary>

            </div>

        </form>

    </x-card>

</div>

@endsection