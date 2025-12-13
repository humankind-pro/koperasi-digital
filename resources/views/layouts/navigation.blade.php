<div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-cyan-600 lg:translate-x-0 lg:static lg:inset-0">
    
    <div class="flex items-center justify-center mt-8">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="w-12 h-12 fill-current text-white" />
            </a>
            <span class="mx-2 text-2xl font-semibold text-white">KOPERASI</span>
        </div>
    </div>

    <nav class="mt-10 px-4 space-y-2">

        {{-- =================== SUPER ADMIN =================== --}}
        @if (Auth::user()->role === 'super_admin')
            <p class="px-4 text-xs font-semibold text-cyan-200 uppercase tracking-wider mt-4 mb-2">Master Data</p>
            
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Dashboard
            </x-nav-link>
            <x-nav-link href="#" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Rekapan
            </x-nav-link>
            <x-nav-link :href="route('karyawans.index')" :active="request()->routeIs('karyawans.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Data Karyawan
            </x-nav-link>
            <x-nav-link :href="route('admins.index')" :active="request()->routeIs('admins.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Data Admin
            </x-nav-link>
            <x-nav-link :href="route('superadmin.anggota.index')" :active="request()->routeIs('superadmin.anggota.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Data Nasabah
            </x-nav-link>

        {{-- =================== KARYAWAN =================== --}}
        @elseif (Auth::user()->role === 'karyawan')
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Dashboard
            </x-nav-link>

            <p class="px-4 text-xs font-semibold text-cyan-200 uppercase tracking-wider mt-6 mb-2">Nasabah</p>
            
            <x-nav-link :href="route('anggota.index')" :active="request()->routeIs('anggota.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Input Nasabah
            </x-nav-link>
            <x-nav-link :href="route('pinjaman.create')" :active="request()->routeIs('pinjaman.create')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Ajukan Pinjaman
            </x-nav-link>
            <x-nav-link :href="route('pinjaman.aktif')" :active="request()->routeIs('pinjaman.aktif')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Bayar Pinjaman
            </x-nav-link>
            <x-nav-link :href="route('anggota.riwayat.search.form')" :active="request()->routeIs('anggota.riwayat.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Cek Riwayat
            </x-nav-link>

            <p class="px-4 text-xs font-semibold text-cyan-200 uppercase tracking-wider mt-6 mb-2">Kepegawaian</p>
            
            <x-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Absensi (Fingerprint)
            </x-nav-link>
            <x-nav-link :href="route('karyawan.gaji.saya')" :active="request()->routeIs('karyawan.gaji.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Gaji Saya
            </x-nav-link>

        {{-- =================== ADMIN =================== --}}
        @elseif (Auth::user()->role === 'admin')
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Dashboard
            </x-nav-link>

            <p class="px-4 text-xs font-semibold text-cyan-200 uppercase tracking-wider mt-6 mb-2">Validasi</p>

            <x-nav-link :href="route('admin.validasi.nasabah.index')" :active="request()->routeIs('admin.validasi.nasabah.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Validasi Nasabah
            </x-nav-link>
            <x-nav-link :href="route('admin.validasi.pinjaman.index')" :active="request()->routeIs('admin.validasi.pinjaman.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Validasi Pinjaman
            </x-nav-link>
            <x-nav-link :href="route('admin.riwayat.pinjaman')" :active="request()->routeIs('admin.riwayat.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Riwayat Pinjaman
            </x-nav-link>
            
            <p class="px-4 text-xs font-semibold text-cyan-200 uppercase tracking-wider mt-6 mb-2">Pribadi</p>
            <x-nav-link :href="route('admin.gaji.saya')" :active="request()->routeIs('admin.gaji.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Gaji Saya
            </x-nav-link>
                        <x-nav-link :href="route('admin.absensi.saya')" :active="request()->routeIs('admin.riwayat.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Absensi (Fingerprint)
            </x-nav-link>

        {{-- =================== SEKERTARIS =================== --}}
        @elseif (Auth::user()->role === 'sekertaris')
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Dashboard
            </x-nav-link>

            <p class="px-4 text-xs font-semibold text-cyan-200 uppercase tracking-wider mt-6 mb-2">Keuangan</p>

            <x-nav-link :href="route('sekertaris.pinjaman.rekap')" :active="request()->routeIs('sekertaris.pinjaman.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Rekap Pinjaman
            </x-nav-link>
            <x-nav-link :href="route('sekertaris.gaji.index')" :active="request()->routeIs('sekertaris.gaji.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Kelola Gaji
            </x-nav-link>
            <x-nav-link :href="route('sekertaris.karyawan.registrasi.create')" :active="request()->routeIs('sekertaris.gaji.*')" class="block px-4 py-2 rounded-md hover:bg-cyan-700 text-white">
                Daftar Absensi Karyawan
            </x-nav-link>
        @endif

    </nav>

    <div class="absolute bottom-0 w-full p-4 bg-cyan-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm font-medium text-white rounded-md hover:bg-cyan-800 transition duration-150">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Log Out
            </button>
        </form>
    </div>
</div>

<div @click="sidebarOpen = false" :class="sidebarOpen ? 'block' : 'hidden'" class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>