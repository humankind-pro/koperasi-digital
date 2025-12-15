<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ================================================= --}}
            {{-- AREA NOTIFIKASI BARU (DITAMBAHKAN DISINI) --}}
            {{-- ================================================= --}}
            @php
                $notifications = Auth::user()->unreadNotifications;
            @endphp

            @if($notifications->count() > 0)
                <div class="mb-8 px-4 sm:px-0">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-bold text-green-800 flex items-center">
                                <span class="animate-pulse mr-2 text-2xl">🔔</span> 
                                Anda memiliki {{ $notifications->count() }} Notifikasi Baru
                            </h3>
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($notifications as $notif)
                                <a href="{{ route('notifikasi.baca', $notif->id) }}" class="block group">
                                    <div class="bg-white p-3 rounded-md border border-green-100 shadow-sm hover:shadow-md hover:border-green-300 transition duration-200 flex items-start">
                                        <div class="flex-shrink-0 pt-1">
                                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 w-full">
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-green-700 transition">
                                                Pinjaman Disetujui!
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $notif->data['pesan'] }}
                                            </p>
                                            <div class="mt-2 flex justify-between items-center">
                                                <span class="text-xs text-gray-400">
                                                    {{ \Carbon\Carbon::parse($notif->data['waktu'])->diffForHumans() }}
                                                </span>
                                                <span class="text-xs font-semibold text-green-600 group-hover:underline">
                                                    Lihat Detail →
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            {{-- ================================================= --}}


            <h2 class="text-2xl font-bold text-gray-800 mb-6 px-4 sm:px-0">Dashboard Karyawan</h2>

            {{-- BAGIAN 1: KARTU STATISTIK & SHORTCUT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 px-4 sm:px-0">
                
                {{-- Kartu Total Nasabah --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-cyan-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Nasabah Anda</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalNasabah }}</p>
                    </div>
                    <div class="p-3 bg-cyan-100 rounded-full text-cyan-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>

                {{-- Shortcut Catat Pembayaran --}}
                <a href="{{ route('pinjaman.aktif') }}" class="group block bg-green-500 hover:bg-green-600 transition duration-300 p-6 rounded-xl shadow-md text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="mb-4 opacity-90">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold">Catat Pembayaran</h3>
                        <p class="text-xs text-green-100 mt-1">Input angsuran nasabah</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 text-green-600 opacity-30 group-hover:scale-110 transition duration-300">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>
                    </div>
                </a>

                {{-- Shortcut Ajukan Pinjaman --}}
                <a href="{{ route('pinjaman.search') }}" class="group block bg-blue-500 hover:bg-blue-600 transition duration-300 p-6 rounded-xl shadow-md text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="mb-4 opacity-90">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold">Ajukan Pinjaman</h3>
                        <p class="text-xs text-blue-100 mt-1">Buat pengajuan baru</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 text-blue-600 opacity-30 group-hover:scale-110 transition duration-300">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                    </div>
                </a>

                {{-- Shortcut Cek Riwayat --}}
                <a href="{{ route('anggota.riwayat.search.form') }}" class="group block bg-purple-500 hover:bg-purple-600 transition duration-300 p-6 rounded-xl shadow-md text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="mb-4 opacity-90">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold">Cek Riwayat</h3>
                        <p class="text-xs text-purple-100 mt-1">Cari data nasabah</p>
                    </div>
                    <div class="absolute -bottom-4 -right-4 text-purple-600 opacity-30 group-hover:scale-110 transition duration-300">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                </a>

            </div>

            {{-- BAGIAN 2: DAFTAR PENGAJUAN NASABAH TERAKHIR --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        5 Pengajuan Pendaftaran Nasabah Terakhir
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Nasabah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Input</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuanNasabahTerbaru as $anggota)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $anggota->nama }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $anggota->no_ktp }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $anggota->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($anggota->status == 'disetujui')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Disetujui
                                                </span>
                                            @elseif($anggota->status == 'ditolak')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Belum ada pengajuan nasabah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</x-app-layout>