@extends('layouts.app')

@section('content')

<x-card>

<form
    action="{{ route('profile.update') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @method('PUT')

    <div class="space-y-5">

        <div>

            <label>Name</label>

            <input
                type="text"
                name="member_name"
                value="{{ $member->member_name }}"
                class="w-full rounded-xl border p-3">

        </div>

        <div>

            <label>Photo</label>

            <input
                type="file"
                name="profile_photo"
                class="w-full rounded-xl border p-3">

        </div>

    </div>

    <button
        class="mt-6 px-6 py-3 rounded-xl bg-black text-white">

        Save

    </button>

</form>

</x-card>

@endsection