@extends('layouts.app')

@section('content')

<x-card>

    <h1 class="text-3xl font-bold mb-6">

        Change Password

    </h1>

    <form
        action="{{ route('profile.password.update') }}"
        method="POST">

        @csrf
        @method('PUT')

        <div class="space-y-5">

            <input
                type="password"
                name="current_password"
                placeholder="Current Password"
                class="w-full rounded-xl border p-3">

            <input
                type="password"
                name="password"
                placeholder="New Password"
                class="w-full rounded-xl border p-3">

            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm Password"
                class="w-full rounded-xl border p-3">

        </div>

        <button
            class="mt-6 px-6 py-3 rounded-xl bg-black text-white">

            Save Password

        </button>

    </form>

</x-card>

@endsection