<!DOCTYPE html>
<html>

<head>

    <title>
        Login
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body
    class="min-h-screen bg-background flex items-center justify-center">

<div
    class="w-full max-w-md">

    <x-card>

        <h1
            class="text-3xl font-bold mb-2">

            Welcome Back

        </h1>

        <p
            class="text-gray-500 mb-6">

            Login to Plainthing

        </p>

        @if(session('success'))

        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">

            {{ session('success') }}

        </div>

        @endif

        @if($errors->any() && !session('scrum_error'))

        <div
            class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-600">

            {{ $errors->first() }}

        </div>

        @endif

        <form
            action="{{ route('login.store') }}"
            method="POST">

            @csrf

            <div
                class="space-y-5">

                <div>

                    <label
                        class="block mb-2 font-medium">

                        Username / Email

                    </label>

                    <input
                        type="text"
                        name="login"
                        value="{{ old('login') }}"
                        placeholder="Masukkan username atau email"
                        class="w-full rounded-xl border border-border p-3">

                </div>

                <div>

                    <label
                        class="block mb-2 font-medium">

                        Password

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-xl border border-border p-3">

                </div>

                <div class="flex items-center justify-between">
                    <div
                        class="flex items-center gap-2">

                        <input
                            type="checkbox"
                            name="remember">

                        <span>
                            Remember Me
                        </span>

                    </div>

                    <a
                        href="{{ route('password.forgot') }}"
                        class="text-sm text-orange-500">

                        Forgot Password?

                    </a>

                </div>

            </div>

            <div
                class="mt-3">

                <button
                    type="submit"
                    class="w-full rounded-xl bg-orange-500 text-white py-3">

                    Login

                </button>

            </div>

            <div
                class="mt-5 text-center">

                <p
                    class="text-gray-500 mb-2.5">

                    Quick Start for Scrum Meeting

                </p>

                <button
                    type="button"
                    id="openScrumModal"
                    class="w-full py-3 rounded-xl bg-gray-200 text-black">

                    Start Scrum

                </button>

            </div>

        </form>

    </x-card>

</div>

{{-- MODAL SCRUM --}}
<div
    id="scrumModal"
    class="
        hidden
        fixed
        inset-0
        bg-black/50
        flex
        items-center
        justify-center
        z-50">

    <div
        class="
            bg-white
            rounded-2xl
            p-6
            w-[450px]
        ">

        <h2
            class="
                text-2xl
                font-bold
                mb-5
            ">

            Start Scrum

        </h2>

        @if(session('scrum_error'))

        <div
            class="
                mb-4
                p-3
                rounded-xl
                bg-red-50
                border
                border-red-200
                text-red-600
            ">

            {{ session('scrum_error') }}

        </div>

        @endif

        @error('password')

        <div
            class="
                mb-4
                p-3
                rounded-xl
                bg-red-50
                border
                border-red-200
                text-red-600
            ">

            {{ $message }}

        </div>

        @enderror

        @error('id_responsible')

        <div
            class="
                mb-4
                p-3
                rounded-xl
                bg-red-50
                border
                border-red-200
                text-red-600
            ">

            {{ $message }}

        </div>

        @enderror

        <form
            action="{{ route('scrum.start.login') }}"
            method="POST">

            @csrf

            <div
                class="mb-4">

                <label
                    class="block mb-2">

                    Scrum Password

                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="
                        w-full
                        border
                        rounded-xl
                        p-3
                    ">

            </div>

            <div
                class="mb-5">

                <label
                    class="block mb-2">

                    Responsibility

                </label>

                <select
                    name="id_responsible"
                    required
                    class="
                        w-full
                        border
                        rounded-xl
                        p-3
                    ">

                    <option value="">
                        Select Responsibility
                    </option>

                    @foreach(
                        $responsibles
                        as $member
                    )

                    <option
                        value="{{ $member->id_member }}">

                        {{ $member->member_name }}

                    </option>

                    @endforeach

                </select>

            </div>

            <button
                type="submit"
                class="
                    w-full
                    py-3
                    rounded-xl
                    bg-orange-500
                    text-white
                ">

                Start Scrum

            </button>

        </form>

    </div>

</div>

<script>

const scrumModal =
    document.getElementById(
        'scrumModal'
    );

document
.getElementById(
    'openScrumModal'
)
.addEventListener(
    'click',
    function()
{
    scrumModal
    .classList
    .remove(
        'hidden'
    );
});

scrumModal
.addEventListener(
    'click',
    function(e)
{
    if(
        e.target.id
        ===
        'scrumModal'
    )
    {
        scrumModal
        .classList
        .add(
            'hidden'
        );
    }
});

@if(
    session('scrum_error')
    ||
    $errors->has('password')
    ||
    $errors->has('id_responsible')
)

scrumModal
.classList
.remove(
    'hidden'
);

@endif

</script>

</body>

</html>