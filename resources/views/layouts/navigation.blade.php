<div class="fixed flex flex-col top-0 left-0 w-64 bg-white h-full border-r border-gray-200 shadow-xl z-50 transition-all duration-300 transform">
    
    <div class="flex items-center justify-center h-20 border-b border-gray-100 bg-white">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                
                <img src="{{ Vite::asset('resources/img/logo-koperasi.png') }}" 
                     alt="Logo" 
                     class="w-10 h-auto drop-shadow-sm filter brightness-110">
                
                <div class="flex flex-col">
                    <span class="text-lg font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 tracking-tight leading-none">
                        KOPERASI
                    </span>
                    <span class="text-[10px] font-bold text-gray-400 tracking-widest leading-none">
                        HARMONI
                    </span>
                </div>
            </a>
        </div>
    </div>

    <div class="overflow-y-auto overflow-x-hidden flex-grow px-4 py-6 space-y-1">

        {{-- =================== SUPER ADMIN =================== --}}
        @if (Auth::user()->role === 'super_admin')
            
            <p class="px-4 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Master Data</p>
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
               Dashboard
            </a>

            <a href="{{ route('karyawans.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('karyawans.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
               Data Karyawan
            </a>

            <a href="{{ route('admins.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('admins.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
               Data Admin
            </a>

            <a href="{{ route('superadmin.anggota.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('superadmin.anggota.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
               Data Nasabah
            </a>

        {{-- =================== KARYAWAN =================== --}}
        @elseif (Auth::user()->role === 'karyawan')
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
               Dashboard
            </a>

            <p class="px-4 mt-6 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Nasabah</p>

            <a href="{{ route('anggota.create') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('anggota.create') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
               Input Nasabah
            </a>

            <a href="{{ route('pinjaman.search') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('pinjaman.*') && !request()->routeIs('pinjaman.aktif') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               Ajukan Pinjaman
            </a>

            <a href="{{ route('pinjaman.aktif') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('pinjaman.aktif') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
               Bayar Pinjaman
            </a>

            <a href="{{ route('anggota.riwayat.search.form') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('anggota.riwayat.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               Cek Riwayat
            </a>

            <p class="px-4 mt-6 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Kepegawaian</p>

            <a href="{{ route('absensi.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('absensi.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 6.667v.01M10 22h4m-9.828-9.828l-2.828 2.828m5.656 0l2.828-2.828m-2.828 2.828l2.828 2.828"></path></svg>
               Absensi (Fingerprint)
            </a>

            <a href="{{ route('karyawan.gaji.saya') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('karyawan.gaji.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
               Gaji Saya
            </a>

        {{-- =================== ADMIN =================== --}}
        @elseif (Auth::user()->role === 'admin')
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
               Dashboard
            </a>

            <p class="px-4 mt-6 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Validasi</p>

            <a href="{{ route('admin.validasi.nasabah.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('admin.validasi.nasabah.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               Validasi Nasabah
            </a>

            <a href="{{ route('admin.validasi.pinjaman.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('admin.validasi.pinjaman.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               Validasi Pinjaman
            </a>

            <a href="{{ route('admin.riwayat.pinjaman') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('admin.riwayat.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               Riwayat Pinjaman
            </a>

            <p class="px-4 mt-6 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Pribadi</p>

            <a href="{{ route('admin.gaji.saya') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('admin.gaji.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
               Gaji Saya
            </a>

            <a href="{{ route('admin.absensi.saya') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('admin.absensi.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 6.667v.01M10 22h4m-9.828-9.828l-2.828 2.828m5.656 0l2.828-2.828m-2.828 2.828l2.828 2.828"></path></svg>
               Absensi (Fingerprint)
            </a>

        {{-- =================== SEKERTARIS =================== --}}
        @elseif (Auth::user()->role === 'sekertaris')
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
               Dashboard
            </a>

            <p class="px-4 mt-6 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Keuangan</p>

            <a href="{{ route('sekertaris.gaji.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('sekertaris.gaji.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
               Kelola Gaji
            </a>

            <p class="px-4 mt-6 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Kelola Data</p>

            <a href="{{ route('sekertaris.karyawan.registrasi.create') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('sekertaris.karyawan.registrasi.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 6.667v.01M10 22h4m-9.828-9.828l-2.828 2.828m5.656 0l2.828-2.828m-2.828 2.828l2.828 2.828"></path></svg>
               Daftar Absensi Karyawan
            </a>

            <a href="{{ route('sekertaris.pinjaman.rekap') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('sekertaris.pinjaman.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
               Rekap Pinjaman
            </a>

            <a href="{{ route('sekertaris.absensi.index') }}" 
               class="flex items-center w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('sekertaris.absensi.*') ? 'bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200 translate-x-1' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700' }}">
               <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               Rekap Absensi
            </a>

        @endif

    </div>

    <div class="border-t border-gray-100 p-4 bg-white">
        <div class="flex items-center gap-3 mb-3 px-2">
            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center text-white font-bold shadow-md">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1 overflow-hidden">
                <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center w-full p-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-all font-semibold text-sm group">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar
            </button>
        </form>
    </div>

</div>