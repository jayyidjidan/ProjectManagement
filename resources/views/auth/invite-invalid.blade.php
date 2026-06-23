<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 antialiased h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white rounded-xl shadow-md overflow-hidden p-8 text-center border border-gray-200">
        
        {{-- Ikon Peringatan --}}
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        {{-- Judul dan Pesan dari Controller --}}
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            {{ $title }}
        </h2>
        
        <p class="text-gray-500 mb-8">
            {{ $message }}
        </p>

        {{-- Tombol Kembali ke Login --}}
        <a href="{{ route('login') }}" class="inline-block w-full bg-gray-900 text-white font-semibold py-3 px-4 rounded-lg hover:bg-gray-800 transition-colors">
            Go to Login Page
        </a>

    </div>

</body>
</html>