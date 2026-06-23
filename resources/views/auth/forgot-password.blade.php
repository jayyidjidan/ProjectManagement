<!DOCTYPE html>
<html>
<head>
    <title>Daily Scrum</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100 p-8">
    <x-card class="w-200">

    <h1 class="text-3xl font-bold mb-6">

        Forgot Password

    </h1>

    <form
        action="{{ route('password.forgot.store') }}"
        method="POST">

        @csrf

        <div class="space-y-5">

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Email"
                class="w-full rounded-xl border border-gray-400 p-3">

            @error('email')

                <p class="text-red-500 text-sm">

                    {{ $message }}

                </p>

            @enderror

            <input
                type="password"
                name="password"
                placeholder="New Password"
                class="w-full rounded-xl border border-gray-400 p-3">

            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm Password"
                class="w-full rounded-xl border border-gray-400 p-3">

        </div>

        <button
            class="mt-6 w-full py-3 rounded-xl bg-orange-500 text-white">

            Reset Password

        </button>

    </form>

</x-card>
</body>
</html>