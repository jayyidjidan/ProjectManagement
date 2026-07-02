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

<body class="min-h-screen flex p-0 lg:p-6 back">

    {{-- Kiri: Background Image --}}
    <div class="hidden lg:flex flex-col justify-between self-stretch items-start p-12  lg:w-2/3 bg-cover bg-center bg-no-repeat rounded-2xl" style="background-image: url('{{ asset('assets/Background.png') }}');">
        <img src="assets/Taskio.png" alt="">
        <div class=" flex flex-col items-start gap-1">
            <h1 class="text-white text-7xl font-medium leading-[1.2] ">See Result, Act Faster</h1>
            <h4 class="text-white/70 text-2xl w-150 leading-[1.2] tracking-[-0.48px]">See Progress from your project directly from one screen, report the problem and finished in time</h4>
        </div>
    </div>

    {{-- Kanan: Form Login --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-background">

            <x-card class="w-full border-0">
                <h1 class="text-5xl w-100 font-bold mb-2 leading-[1.2]">
                    Login To Your Account
                </h1>

                <p class="text-gray-500 mb-6">
                    Login to Plainthing
                </p>

                @if(session('success'))

                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">
                    {{ session('success') }}
                </div>

                @endif

                @if($errors->any() && !session('scrum_error'))

                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-600">
                    {{ $errors->first() }}
                </div>

                @endif

                <form
                    action="{{ route('login.store') }}"
                    method="POST">

                    @csrf

                    <div class="space-y-5">

                        <div>

                            <label class="block mb-2 font-medium">
                                Username / Email
                            </label>

                            <input
                                type="text"
                                name="login"
                                value="{{ old('login') }}"
                                placeholder="Masukkan username atau email"
                                class="w-full rounded-xl p-3 border border-gray-200">

                        </div>

                        <div>

                            <label class="block mb-2 font-medium">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="w-full rounded-xl p-3 border border-gray-200">

                        </div>

                        <div class="flex items-center justify-between">

                            <div class="flex items-center gap-2">

                                <input
                                    type="checkbox"
                                    name="remember">

                                <span>Remember Me</span>

                            </div>

                            <a
                                href="{{ route('password.forgot') }}"
                                class="text-sm text-orange-500">
                                Forgot Password?
                            </a>

                        </div>

                    </div>

                    <div class="mt-3">
                        
                        <x-button-primary class="w-full">
                            Login
                        </x-button-primary>

                        <!-- <button
                            type="submit"
                            class="w-full rounded-xl bg-orange-500 text-white py-3">
                            Login
                        </button> -->

                    </div>

                    <div class="mt-5 text-center">

                        <p class="text-gray-500 mb-2.5">
                            Quick Start for Scrum Meeting
                        </p>

                        <x-button-secondary type="button" id="openScrumModal" class=" border-orange-500 text-orange-500 w-full">
                            Start Scrum
                        </x-button-secondary>

                    </div>

                </form>

            </x-card>

    </div>

    {{-- MODAL SCRUM --}}
    <div
        id="scrumModal"
        class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white rounded-2xl p-6 w-[450px]">

            <h2 class="text-2xl font-bold mb-5">
                Start Scrum
            </h2>

            @if(session('scrum_error'))

            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600">
                {{ session('scrum_error') }}
            </div>

            @endif

            @error('password')

            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600">
                {{ $message }}
            </div>

            @enderror

            @error('id_responsible')

            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600">
                {{ $message }}
            </div>

            @enderror

            <form
                action="{{ route('scrum.start.login') }}"
                method="POST">

                @csrf

                <div class="mb-4">

                    <label class="block mb-2">
                        Scrum Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full border rounded-xl p-3">

                </div>

                <div class="mb-5">

                    <label class="block mb-2">
                        Responsibility
                    </label>

                    <select
                        name="id_responsible"
                        required
                        class="w-full border rounded-xl p-3">

                        <option value="">
                            Select Responsibility
                        </option>

                        @foreach($responsibles as $member)

                        <option value="{{ $member->id_member }}">
                            {{ $member->member_name }}
                        </option>

                        @endforeach

                    </select>

                </div>

                <button
                    type="submit"
                    class="w-full py-3 rounded-xl bg-orange-500 text-white">
                    Start Scrum
                </button>

            </form>

        </div>

    </div>

    <script>

        const scrumModal = document.getElementById('scrumModal');

        document
            .getElementById('openScrumModal')
            .addEventListener('click', function () {
                scrumModal.classList.remove('hidden');
            });

        scrumModal.addEventListener('click', function (e) {
            if (e.target.id === 'scrumModal') {
                scrumModal.classList.add('hidden');
            }
        });

        @if(session('scrum_error') || $errors->has('password') || $errors->has('id_responsible'))
            scrumModal.classList.remove('hidden');
        @endif

    </script>

</body>

</html>