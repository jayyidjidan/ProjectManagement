@extends('layouts.app')

@section('content')

<div class="max-w-2xl">
    <div class="mb-6 space-y-2">
    <h1 class="text-4xl font-bold">
            Project Types
    </h1>

    <p class="text-gray-500">
        Add more Project Types to your company
    </p>
</div>

<x-card> 
    <form
        method="POST"
        action="{{ route('tipes.store') }}">

        @csrf

        <div class="space-y-4">

            <div>

                <label
                    for="nama_tipe"
                    class="block mb-2">
                    Project Type
                </label>

                <x-input
                    id="nama_tipe"
                    name="nama_tipe"  
                    value="{{ old('nama_tipe') }}" />

            </div>

        </div>

        <div class="mt-6 flex gap-3">

            <x-button-primary type="submit">
                Save
            </x-button-primary>

            
                <x-button-secondary>
                    <a href="{{ route('tipes.index') }}">Cancel</a>
                </x-button-secondary>

        </div>

    </form>

</x-card>
</div>


@endsection