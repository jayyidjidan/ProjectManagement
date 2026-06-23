@extends('layouts.app')

@section('content')

<div class="max-w-2xl">
    <div class="mb-6 space-y-2">
    <h1 class="text-4xl font-bold">
        Positions
    </h1>

    <p class="text-gray-500">
        Add more positions to your company
    </p>
</div>

<x-card> 
    <form
        method="POST"
        action="{{ route('jabatans.store') }}">

        @csrf

        <div class="space-y-4">

            <div>

                <label
                    for="position_name"
                    class="block mb-2">
                    Position Name
                </label>

                <x-input
                    id="position_name"
                    name="position_name"
                    value="{{ old('position_name') }}" />

            </div>

        </div>

        <div class="mt-6 flex gap-3">

            <x-button-primary type="submit">
                Save
            </x-button-primary>

            
                <x-button-secondary>
                    <a href="{{ route('jabatans.index') }}">Cancel</a>
                </x-button-secondary>

        </div>

    </form>

</x-card>
</div>


@endsection