<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Riwayat Validasi Pinjaman
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2 font-semibold text-gray-500 uppercase text-xs">
                            <div class="w-1/4">Nama Nasabah</div>
                            <div class="w-1/4">Jumlah Disetujui</div>
                            <div class="w-1/4">Tanggal Validasi</div>
                            <div class="w-1/4 text-center">Status</div>
                        </div>

                        @forelse ($riwayatValidasi as $pinjaman)
                            <div class="flex items-center py-3 border-b">
                                <div class="w-1/4">
                                    <p class="font-semibold text-gray-800">{{ $pinjaman->anggota->nama ?? 'N/A' }}</p>
                                </div>
                                <div class="w-1/4">
                                    <p class="text-sm text-gray-600">
                                        {{-- Tampilkan jumlah jika disetujui, jika tidak, tampilkan strip --}}
                                        @if($pinjaman->status == 'disetujui')
                                            Rp {{ number_format($pinjaman->jumlah_disetujui, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div class="w-1/4">
                                    <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($pinjaman->tanggal_validasi)->format('d M Y') }}</p>
                                </div>
                                <div class="w-1/4 text-center">
                                    {{-- Badge Status Dinamis --}}
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($pinjaman->status == 'disetujui') bg-green-100 text-green-800 
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($pinjaman->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Belum ada riwayat validasi pinjaman.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>