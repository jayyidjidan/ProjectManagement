@extends('layouts.app')

@section('content')

@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-end justify-between mb-6">

    <div class="space-y-2">

        <h1 class="text-4xl font-bold">
            Clients
        </h1>

        <p class="text-gray-500">
            Manage your clients
        </p>

    </div>

    

<div class="flex items-center gap-3">

        <form method="GET">

            <select
                name="source"
                onchange="this.form.submit()"
                class="h-12 px-4 rounded-2xl border border-border bg-white">

                <option value="">
                    All Sources
                </option>

                @foreach($sources as $source)

                    <option
                        value="{{ $source->id_sumberklien }}"
                        @selected(request('source') == $source->id_sumberklien)>

                        {{ $source->nama_sumber }}

                    </option>

                @endforeach

            </select>

        </form>

        <a href="{{ route('clients.create') }}">
            <x-button-primary>
                Add Client
            </x-button-primary>
        </a>

    </div>

</div>

<x-card>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b border-border">

                    <th class="text-left p-4 w-20">
                        No
                    </th>

                    <th class="text-left p-4">
                        Client Name
                    </th>

                    <th class="text-left p-4">
                        Phone
                    </th>

                    <th class="text-left p-4">
                        Email
                    </th>

                    <th class="text-left p-4">
                        Source
                    </th>

                    <th class="text-left p-4">
                        Country
                    </th>

                    <th class="text-right p-4 w-48">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($clients as $client)

                    <tr class="border-b border-border">

                        <td class="p-4">
                            {{ $clients->firstItem() + $loop->index }}
                        </td>

                        <td class="p-4">
                            {{ $client->nama_klien }}
                        </td>

                        <td class="p-4">
                            {{ $client->no_telp }}
                        </td>

                        <td class="p-4">
                            {{ $client->email }}
                        </td>

                        <td class="p-4">
                            {{ $client->sumberKlien->nama_sumber ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $client->asal_negara }}
                        </td>

                        <td class="p-4">

                            <div class="flex justify-end gap-2">

                                <a
                                    href="{{ route('clients.edit', $client) }}"
                                    class="px-4 py-2 rounded-xl border border-border hover:bg-gray-100">

                                    Edit

                                </a>

                                <form
                                    action="{{ route('clients.destroy', $client) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus client ini?')">

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

                        <td colspan="7" class="text-center py-12 text-gray-500">
                            No Data Available
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-card>

<div class="mt-6">
    {{ $clients->links() }}
</div>

@endsection