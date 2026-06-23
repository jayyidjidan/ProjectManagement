@extends('layouts.app')

@section('content')


<div class="max-w-2xl">
    <div class="mb-6 space-y-2">
        <h1 class="text-4xl font-bold">
            Status Member
        </h1>

        <p class="text-gray-500">
            Edit status members in your company
        </p>
    </div>

<x-card>

    <form
        method="POST"
        action="{{ route('status-members.update', $status_member) }}">

        @csrf
        @method('PUT')

        <div class="space-y-4">

            <div>

                <label
                    for="status_name"
                    class="block mb-2">
                    Status Name
                </label>

                <x-input
                    id="status_name"
                    name="status_name"
                    value="{{ old('status_name', $status_member->status_name) }}" />

            </div>

        </div>

        <div class="mt-6 flex gap-3">

            <x-button-primary type="submit">
                Save
            </x-button-primary>

            <x-button-secondary>
                <a href="{{ route('status-members.index') }}">Cancel</a>
            </x-button-secondary>

        </div>

    </form>

</x-card>
</div>


@endsection