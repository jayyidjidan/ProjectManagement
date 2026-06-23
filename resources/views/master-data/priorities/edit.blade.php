@extends('layouts.app')

@section('content')

<div class="max-w-2xl">

    <div class="mb-6 space-y-2">

        <h1 class="text-4xl font-bold">
            Priorities
        </h1>

        <p class="text-gray-500">
            Edit priorities in your company
        </p>

    </div>

    <x-card>

        <form
            method="POST"
            action="{{ route('priorities.update', $priority) }}">

            @csrf
            @method('PUT')

            <div class="space-y-4">

                <div>

                    <label
                        for="priority_name"
                        class="block mb-2">

                        Priority Name

                    </label>

                    <x-input
                        id="priority_name"
                        name="priority_name"
                        value="{{ old('priority_name', $priority->priority_name) }}" />

                </div>

            </div>

            <div class="mt-6 flex gap-3">

                <x-button-primary type="submit">
                    Save
                </x-button-primary>

                <a href="{{ route('priorities.index') }}">
                    <x-button-secondary>
                        Cancel
                    </x-button-secondary>
                </a>

            </div>

        </form>

    </x-card>

</div>

@endsection