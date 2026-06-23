@extends('layouts.app')

@section('content')

<div class="flex items-end justify-between mb-6">

<div>

    <h1 class="text-4xl font-bold">
        Send Invitation
    </h1>

    <p class="text-gray-500 mt-2">
        Invite a new employee
    </p>

</div>

<a href="{{ route('invitations.index') }}">

    <x-button-secondary>
        Back
    </x-button-secondary>

</a>

</div>

<x-card>

<form
    action="{{ route('invitations.store') }}"
    method="POST">

@csrf

<div class="space-y-6">

    <div>

        <label class="block mb-2 font-medium">
            Email
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="w-full rounded-xl border border-border p-3"
            required>
        
        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label class="block mb-2 font-medium">
            Role
        </label>

        <select
            name="id_role"
            class="w-full rounded-xl border border-border p-3"
            required>

            <option value="">
                Select Role
            </option>

            @foreach($roles as $role)

                <option
                    value="{{ $role->id_role }}">

                    {{ $role->role_name }}

                </option>

            @endforeach

        </select>

    </div>

</div>

<div class="flex justify-end mt-8">

    <x-button-primary>
        Send Invitation
    </x-button-primary>

</div>

</form>

</x-card>

@endsection
