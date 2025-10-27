<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Data Pengajuan Nasabah
                    </h3>

                    <a href="{{ route('anggota.create') }}" class="inline-block mb-6 px-4 py-2 bg-cyan-500 border border-transparent rounded-md text-sm text-white hover:bg-cyan-600 transition ease-in-out duration-150">
                        Ajukan Nasabah Baru
                    </a>
                    <a href="{{ route('karyawan.pinjaman.aktif') }}" class="inline-block mb-6 ml-4 px-4 py-2 bg-green-500 border rounded-md text-sm text-white hover:bg-green-600 ...">
    Lihat Pinjaman Aktif & Pembayaran
</a>

                    {{-- Menampilkan pesan sukses --}}
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Container untuk list data --}}
                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2">
                            <div class="w-2/5"><span class="text-xs font-semibold text-gray-500 uppercase">Nama</span></div>
                            <div class="w-2/5"><span class="text-xs font-semibold text-gray-500 uppercase">No KTP</span></div>
                            <div class="w-1/5"><span class="text-xs font-semibold text-gray-500 uppercase">Status</span></div>
                        </div>

                        @forelse ($anggotas as $anggota)
                            <div class="flex items-center py-3 border-b">
                                <div class="w-2/5">
                                    <p class="font-semibold text-gray-800">{{ $anggota->nama }}</p>
                                </div>
                                <div class="w-2/5">
                                    <p class="text-sm text-gray-600">{{ $anggota->no_ktp }}</p>
                                </div>
                                <div class="w-1/5">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($anggota->status == 'disetujui') bg-green-100 text-green-800 
                                        @elseif($anggota->status == 'ditolak') bg-red-100 text-red-800 
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($anggota->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Belum ada data pengajuan nasabah.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>