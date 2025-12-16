<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi Harmoni</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-screen overflow-hidden bg-gray-50">
    
    <div class="flex h-full w-full">
        
        <div class="hidden lg:flex flex-1 relative items-center justify-center bg-gradient-to-br from-cyan-600 via-blue-600 to-blue-800 overflow-hidden">
            
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-80 h-80 bg-cyan-400 opacity-20 rounded-full blur-3xl"></div>

            <div class="relative z-10 text-center px-12">
                
                <div class="mb-8">
                    <img src="{{ Vite::asset('resources/img/logo-koperasi.png') }}" 
                         alt="Logo Koperasi Indonesia" 
                         class="w-48 h-auto mx-auto drop-shadow-2xl filter brightness-110 hover:scale-105 transition-transform duration-300">
                </div>

                <h2 class="text-4xl font-bold text-white mb-4 tracking-tight">Koperasi Harmoni</h2>
                <p class="text-cyan-100 text-lg max-w-md mx-auto leading-relaxed">
                    Sistem Manajemen Koperasi Terintegrasi.<br>
                    Aman, Transparan, dan Terpercaya.
                </p>
            </div>
        </div>

        <div class="flex-1 flex flex-col items-center justify-center p-8 bg-white relative">
            
            <div class="w-full max-w-[400px]">
                
                <div class="lg:hidden flex flex-col items-center mb-8">
                    <img src="{{ Vite::asset('resources/img/logo-koperasi.png') }}" 
                         alt="Logo Koperasi" 
                         class="w-24 h-auto mb-4 drop-shadow-md">
                    <h2 class="text-2xl font-bold text-gray-800">Koperasi Harmoni</h2>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Selamat Datang!</h1>
                    <p class="text-gray-500">Silakan masuk untuk mengakses akun Anda.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Email / Username</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200"
                                placeholder="nama@koperasi.com">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 ml-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2 ml-1">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center ml-1">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 px-6 text-white font-bold text-lg rounded-xl bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 hover:from-cyan-700 hover:via-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-500/30 shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 transition-all duration-200 flex justify-center items-center group">
                        Masuk Sekarang
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>

            </div>

            <div class="absolute bottom-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Koperasi Harmoni. All rights reserved.
            </div>
        </div>
    </div>

</body>
</html>