@extends('layouts.app')

@section('content')

<div class="max-w-2xl">
    <div class="mb-6 space-y-2">
    <h1 class="text-4xl font-bold">
        Categories
    </h1>

    <p class="text-gray-500">
        Add more Categories to your company
    </p>
</div>

<x-card> 
    <form
        method="POST"
        action="{{ route('kategoris.store') }}">

        @csrf

        <div class="space-y-4">

            <div>

                <label
                    for="nama_kategori"
                    class="block mb-2">
                    Category Name
                </label>

                <x-input
                    id="nama_kategori"
                    name="nama_kategori"  
                    value="{{ old('nama_kategori') }}" />

            </div>

        </div>

        <div class="mt-6 flex gap-3">

            <x-button-primary type="submit">
                Save
            </x-button-primary>

            
                <x-button-secondary>
                    <a href="{{ route('kategoris.index') }}">Cancel</a>
                </x-button-secondary>

        </div>

    </form>

</x-card>
</div>


@endsection