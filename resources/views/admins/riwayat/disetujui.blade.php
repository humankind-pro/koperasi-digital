<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Riwayat Nasabah Disetujui
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2 font-semibold text-gray-500 uppercase text-xs">
                            <div class="w-1/4">Nama Nasabah</div>
                            <div class="w-1/4">Diajukan Oleh</div>
                            <div class="w-1/4">Disetujui Oleh</div>
                            <div class="w-1/4">Tanggal Disetujui</div>
                        </div>

                        @forelse ($pengajuanDisetujui as $anggota)
                            <div class="flex items-center py-3 border-b">
                                <div class="w-1/4">
                                    <p class="font-semibold text-gray-800">{{ $anggota->nama }}</p>
                                </div>
                                <div class="w-1/4">
                                    <p class="text-sm text-gray-600">{{ $anggota->dibuatOleh->name ?? 'N/A' }}</p>
                                </div>
                                <div class="w-1/4">
                                    <p class="text-sm text-gray-600">{{ $anggota->divalidasiOleh->name ?? 'N/A' }}</p>
                                </div>
                                <div class="w-1/4">
                                    <p class="text-sm text-gray-600">{{ $anggota->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Belum ada riwayat pengajuan yang disetujui.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>