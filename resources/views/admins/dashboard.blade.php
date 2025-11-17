<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard Admin</h2>
                <span class="text-sm text-gray-500">{{ now()->format('l, d F Y') }}</span>
            </div>

            {{-- BAGIAN 1: KARTU STATISTIK & SHORTCUT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 px-4 sm:px-0">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalKaryawan }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-indigo-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Nasabah</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalNasabah }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>

                <a href="{{ route('admin.riwayat.pinjaman') }}" class="group block bg-purple-600 hover:bg-purple-700 transition duration-300 p-6 rounded-xl shadow-md text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="mb-4 opacity-90">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold">Riwayat Pinjaman</h3>
                        <p class="text-xs text-purple-200 mt-1">Lihat arsip pinjaman</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 text-purple-800 opacity-30 group-hover:scale-110 transition duration-300">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                    </div>
                </a>

                <a href="{{ route('admin.validasi.nasabah.index') }}" class="group block bg-orange-500 hover:bg-orange-600 transition duration-300 p-6 rounded-xl shadow-md text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="mb-4 opacity-90">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold">Validasi Nasabah</h3>
                        <p class="text-xs text-orange-100 mt-1">Cek input dari karyawan</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 text-orange-700 opacity-30 group-hover:scale-110 transition duration-300">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </a>

            </div>

            {{-- BAGIAN 2: STATUS PEKERJAAN TERKINI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 md:p-8 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            Status Validasi Pinjaman
                        </h3>
                        @if($pendingPinjaman > 0)
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $pendingPinjaman }} Menunggu</span>
                        @else
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Semua Beres</span>
                        @endif
                    </div>
                    
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <p class="text-gray-500 mb-4">Ada {{ $pendingPinjaman }} pengajuan pinjaman baru yang menunggu persetujuan Anda.</p>
                        <a href="{{ route('admin.validasi.pinjaman.index') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 transition ease-in-out duration-150">
                            Proses Validasi Pinjaman Sekarang
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>