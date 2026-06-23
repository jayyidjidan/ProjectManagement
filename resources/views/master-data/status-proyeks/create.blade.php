@extends('layouts.app')

@section('content')

<div class="max-w-2xl">
    <div class="mb-6 space-y-2">
    <h1 class="text-4xl font-bold">
        Project Status
    </h1>

    <p class="text-gray-500">
        Add more project statuses to your company
    </p>
</div>

<x-card> 
    <form
        method="POST"
        action="{{ route('status-proyeks.store') }}">

        @csrf

        <div class="space-y-4">

            <div>

                <label
                    for="nama_status"
                    class="block mb-2">
                    Status Name
                </label>

                <x-input
                    id="nama_status"
                    name="nama_status"
                    value="{{ old('nama_status') }}" />

            </div>

        </div>

        <div class="mt-6 flex gap-3">

            <x-button-primary type="submit">
                Save
            </x-button-primary>

            
                <x-button-secondary>
                    <a href="{{ route('status-proyeks.index') }}">Cancel</a>
                </x-button-secondary>

        </div>

    </form>

</x-card>
</div>


@endsection