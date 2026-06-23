@extends('layouts.app')

@section('content')

<div class="max-w-2xl">
    <div class="mb-6 space-y-2">
    <h1 class="text-4xl font-bold">
            Client Sources
    </h1>

    <p class="text-gray-500">
        Add more Client Sources to your company
    </p>
</div>

<x-card> 
    <form
        method="POST"
        action="{{ route('sumber-kliens.store') }}">

        @csrf

        <div class="space-y-4">

            <div>

                <label
                    for="nama_sumber"
                    class="block mb-2">
                    Client Source
                </label>

                <x-input
                    id="nama_sumber"
                    name="nama_sumber"  
                    value="{{ old('nama_sumber') }}" />

            </div>

        </div>

        <div class="mt-6 flex gap-3">

            <x-button-primary type="submit">
                Save
            </x-button-primary>

            
                <x-button-secondary>
                    <a href="{{ route('sumber-kliens.index') }}">Cancel</a>
                </x-button-secondary>

        </div>

    </form>

</x-card>
</div>


@endsection