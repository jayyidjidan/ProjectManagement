@extends('layouts.app')

@section('content')

<div class="max-w-2xl">
    <div class="mb-6 space-y-2">
    <h1 class="text-4xl font-bold">
        Roles
    </h1>

    <p class="text-gray-500">
        Add more roles to your company
    </p>
</div>

<x-card> 
    <form
        method="POST"
        action="{{ route('roles.store') }}">

        @csrf

        <div class="space-y-4">

            <div>

                <label
                    for="role_name"
                    class="block mb-2">
                    Role Name
                </label>

                <x-input
                    id="role_name"
                    name="role_name"
                    value="{{ old('role_name') }}" />

            </div>

        </div>

        <div class="mt-6 flex gap-3">

            <x-button-primary type="submit">
                Save
            </x-button-primary>

            
                <x-button-secondary>
                    <a href="{{ route('roles.index') }}">Cancel</a>
                </x-button-secondary>

        </div>

    </form>

</x-card>
</div>


@endsection