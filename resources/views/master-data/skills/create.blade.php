@extends('layouts.app')

@section('content')

<div class="max-w-2xl">

    <div class="mb-6 space-y-2">

        <h1 class="text-4xl font-bold">
            Skills
        </h1>

        <p class="text-gray-500">
            Add more skills to your company
        </p>

    </div>

    <x-card>

        <form
            method="POST"
            action="{{ route('skills.store') }}">

            @csrf

            <div class="space-y-4">

                <div>

                    <label
                        for="skill_name"
                        class="block mb-2">

                        Skill Name

                    </label>

                    <x-input
                        id="skill_name"
                        name="skill_name"
                        value="{{ old('skill_name') }}" />

                </div>

            </div>

            <div class="mt-6 flex gap-3">

                <x-button-primary type="submit">
                    Save
                </x-button-primary>

                <a href="{{ route('skills.index') }}">
                    <x-button-secondary>
                        Cancel
                    </x-button-secondary>
                </a>

            </div>

        </form>

    </x-card>

</div>

@endsection