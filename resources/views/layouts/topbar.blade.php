@php

$user =
    auth()->user();

$name =
    $user->member?->member_name
    ?? $user->username;

$role =
    $user->role?->role_name
    ?? '-';

$initial =
    strtoupper(
        substr(
            $name,
            0,
            1
        )
    );

$photo =
    $user->member?->profile_photo;

@endphp

<header
    class="h-20 bg-sidebar border-b border-border px-8 flex items-center justify-between">

    {{-- Search --}}
    <div class="w-[420px]">

        <x-input
            type="search"
            id="global-search"
            value="{{ request('q') }}" 
            placeholder="Search in this page..." 
            class="w-full"
            onkeypress="handleSearch(event)"/>

    </div>

    <div class="flex items-center gap-4">

        {{-- Notification --}}
        <button
            class="w-12 h-12 rounded-2xl border border-border bg-white flex items-center justify-center">

            🔔

        </button>

        {{-- User Profile --}}
        <div
            x-data="{ open:false }"
            class="relative">

            <button
                @click="open=!open"
                class="flex items-center gap-3 px-4 h-12 rounded-2xl border border-border bg-white hover:bg-gray-50">

                @if($photo)

                    <img
                        src="{{ asset('storage/' . $photo) }}"
                        alt="{{ $name }}"
                        class="w-8 h-8 rounded-full object-cover">

                @else

                    <div
                        class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center font-semibold">

                        {{ $initial }}

                    </div>

                @endif

                <div
                    class="flex flex-col items-start leading-tight">

                    <span
                        class="font-medium text-sm">

                        {{ $name }}

                    </span>

                    <span
                        class="text-xs text-gray-500">

                        {{ $role }}

                    </span>

                </div>

                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7">
                    </path>

                </svg>

            </button>

            {{-- Dropdown --}}
            <div
                x-show="open"
                @click.away="open=false"
                x-transition
                class="absolute right-0 mt-2 w-64 bg-white border border-border rounded-2xl shadow-lg overflow-hidden z-50">

                {{-- Header --}}
                <div
                    class="px-4 py-4 border-b border-border">

                    <div
                        class="font-semibold">

                        {{ $name }}

                    </div>

                    <div
                        class="text-sm text-gray-500">

                        {{ $user->email }}

                    </div>

                </div>

                {{-- Profile --}}
                <a
                    href="{{ route('profile.show') }}"
                    class="block px-4 py-3 hover:bg-gray-50">

                    👤 Profile

                </a>

                {{-- Edit Profile --}}
                <a
                    href="{{ route('profile.edit') }}"
                    class="block px-4 py-3 hover:bg-gray-50">

                    ✏️ Edit Profile

                </a>

                {{-- Change Password --}}
                <a
                    href="{{ route('profile.password') }}"
                    class="block px-4 py-3 hover:bg-gray-50">

                    🔒 Change Password

                </a>

                {{-- Logout --}}
                <form
                    action="{{ route('logout') }}"
                    method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50">

                        🚪 Logout

                    </button>

                </form>

            </div>

        </div>

    </div>

    <script>
        function handleSearch(event) {
            // Jalankan hanya jika user menekan tombol Enter
            if (event.key === 'Enter') {
                event.preventDefault();
                
                const keyword = event.target.value.trim();
                const currentUrl = new URL(window.location.href);

                if (keyword) {
                    // Set parameter 'q' di URL tanpa mengubah path halaman
                    currentUrl.searchParams.set('q', keyword);
                } else {
                    // Jika input kosong, hapus parameter 'q' dari URL
                    currentUrl.searchParams.delete('q');
                }

                // Refresh halaman dengan URL baru yang membawa parameter search
                window.location.href = currentUrl.toString();
            }
        }
    </script>

</header>