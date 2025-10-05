<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi Harmoni</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden">
    <div class="flex h-screen">
        <!-- Left Section with Logo -->
        <div class="flex-1 bg-gradient-to-br from-cyan-400 to-cyan-500 flex items-center justify-center p-10">
            <div class="max-w-md w-full">
                <!-- Ganti dengan path logo Anda -->
                <img src="{{ Vite::asset('resources/img/logo-koperasi.png') }}" alt="Logo Koperasi Indonesia" class="w-full h-auto">
            </div>
        </div>

        <!-- Right Section with Form -->
        <div class="flex-1 bg-[#EAE2B7] flex flex-col items-center justify-center p-10 relative">
            <div class="w-full max-w-md">
                <h1 class="text-5xl font-bold text-[#1a3a52] mb-16 text-center">Login</h1>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-5 p-3 rounded-lg bg-green-100 text-green-800 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username/Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-base font-medium text-[#1a3a52] mb-2">
                            Username
                        </label>
                        <input 
                            id="email" 
                            class="w-full px-5 py-3.5 text-base border-0 rounded-lg bg-white/60 text-gray-800 placeholder-gray-400 outline-none focus:bg-white/90 focus:ring-2 focus:ring-cyan-400 transition-all" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="Enter your Username here"
                            required 
                            autofocus 
                            autocomplete="username"
                        >
                        @error('email')
                            <div class="mt-1.5 text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-base font-medium text-[#1a3a52] mb-2">
                            Password
                        </label>
                        <input 
                            id="password" 
                            class="w-full px-5 py-3.5 text-base border-0 rounded-lg bg-white/60 text-gray-800 placeholder-gray-400 outline-none focus:bg-white/90 focus:ring-2 focus:ring-cyan-400 transition-all" 
                            type="password" 
                            name="password" 
                            placeholder="Enter your Password here"
                            required 
                            autocomplete="current-password"
                        >
                        @error('password')
                            <div class="mt-1.5 text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3.5 text-lg font-semibold text-[#1a3a52] bg-cyan-400 rounded-lg hover:bg-cyan-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-cyan-400/40 transition-all duration-300 mt-8"
                    >
                        Enter
                    </button>

                    <!-- Contact Link -->
                    <div class="text-center mt-5 text-sm text-gray-600">
                        Any problem? <a href="#" class="text-[#1a3a52] font-semibold hover:underline">contact centre</a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="absolute bottom-8 text-sm font-medium text-[#1a3a52]">
                Koperasi Harmoni
            </div>
        </div>
    </div>
</body>
</html>