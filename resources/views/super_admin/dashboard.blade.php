<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 px-4 sm:px-0">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard Super Admin</h2>
                <p class="text-sm text-gray-500">Ringkasan data seluruh sistem koperasi.</p>
            </div>

            {{-- GRID KARTU STATISTIK --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 px-4 sm:px-0">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Admin</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAdmin }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        {{-- Icon User Shield --}}
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalKaryawan }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full text-green-600">
                        {{-- Icon Briefcase --}}
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Nasabah</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalNasabah }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full text-purple-600">
                        {{-- Icon Users --}}
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>

            </div>

            {{-- SHORTCUT MENU --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 sm:px-0">
                <a href="{{ route('admins.index') }}" class="block p-4 bg-white rounded-lg shadow hover:bg-gray-50 text-center border border-gray-200">
                    <span class="font-semibold text-blue-600">Kelola Data Admin &rarr;</span>
                </a>
                <a href="{{ route('karyawans.index') }}" class="block p-4 bg-white rounded-lg shadow hover:bg-gray-50 text-center border border-gray-200">
                    <span class="font-semibold text-green-600">Kelola Data Karyawan &rarr;</span>
                </a>
                <a href="{{ route('superadmin.anggota.index') }}" class="block p-4 bg-white rounded-lg shadow hover:bg-gray-50 text-center border border-gray-200">
                    <span class="font-semibold text-purple-600">Lihat Semua Nasabah &rarr;</span>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>