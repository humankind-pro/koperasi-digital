<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Daftar Nasabah Saya</h2>
                    <p class="text-gray-500 text-sm">Kelola data nasabah yang Anda daftarkan.</p>
                </div>
                <a href="{{ route('anggota.create') }}" 
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white font-bold rounded-xl shadow-lg transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Nasabah Baru
                </a>
            </div>

            {{-- NOTIFIKASI SUKSES (HTML SUPPORT) --}}
            @if (session('success'))
                <div class="mb-8 bg-white border-l-4 border-green-500 rounded-xl shadow-md overflow-hidden animate-fade-in-down">
                    <div class="p-4 flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">Berhasil!</p>
                            {{-- Gunakan {!! !!} karena controller mengirim tag HTML (<br>, <strong>) --}}
                            <div class="mt-1 text-sm text-gray-600 leading-relaxed">
                                {!! session('success') !!}
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tabel Data --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Identitas</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak & Alamat</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Input</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($anggotas as $anggota)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    {{-- Identitas --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-cyan-100 rounded-full flex items-center justify-center text-cyan-600 font-bold text-lg">
                                                {{ substr($anggota->nama, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $anggota->nama }}</div>
                                                <div class="text-xs text-gray-500 font-mono">{{ $anggota->kode_anggota }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kontak --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            {{ $anggota->nomor_telepon }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 truncate w-48" title="{{ $anggota->alamat }}">
                                            {{ Str::limit($anggota->alamat, 30) }}
                                        </div>
                                    </td>

                                    {{-- Pekerjaan --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $anggota->pekerjaan ?? '-' }}</div>
                                        <div class="text-xs font-bold text-green-600">
                                            Rp {{ number_format($anggota->pendapatan_bulanan, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($anggota->status == 'pending')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                ⏳ Menunggu Validasi
                                            </span>
                                        @elseif($anggota->status == 'disetujui' || $anggota->status == 'aktif')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                ✅ Aktif
                                            </span>
                                        @elseif($anggota->status == 'ditolak')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                ❌ Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $anggota->created_at->format('d M Y') }}
                                        <span class="block text-xs text-gray-400">{{ $anggota->created_at->format('H:i') }}</span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($anggota->status == 'disetujui' || $anggota->status == 'aktif')
                                            <a href="{{ route('pinjaman.create.existing', $anggota->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded-md transition hover:bg-blue-100">
                                                + Ajukan Pinjaman
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Menunggu persetujuan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <p class="text-gray-500 text-lg font-medium">Belum ada nasabah yang didaftarkan.</p>
                                            <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Nasabah Baru" untuk mulai.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $anggotas->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Style Tambahan untuk Animasi --}}
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
    </style>
</x-app-layout>