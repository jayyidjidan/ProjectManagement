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
                Categories
            </h1>

            <p class="text-gray-500">
                Manage the Categories in your company
            </p>
        </div>

        <a href="{{ route('kategoris.create') }}">
            <x-button-primary>
                Add Category
            </x-button-primary>
        </a>

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
                            Category
                        </th>

                        <th class="text-right p-4 w-48">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($kategoris as $kategori)

                        <tr class="border-b border-border">

                            <td class="p-4">

                                {{ $kategoris->firstItem() + $loop->index }}

                            </td>

                            <td class="p-4">

                                {{ $kategori->nama_kategori }}

                            </td>

                            <td class="p-4">

                                <div class="flex justify-end gap-2">

                                    <a
                                        href="{{ route('kategoris.edit', $kategori) }}"
                                        class="px-4 py-2 rounded-xl border border-border hover:bg-gray-100 transition">

                                        Edit

                                    </a>

                                    <form
                                        action="{{ route('kategoris.destroy', $kategori) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 transition">

                                            Delete

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="text-center py-12 text-gray-500">

                                No Data Available

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </x-card>

    <div class="mt-6">

        {{ $kategoris->links() }}

    </div>

@endsection