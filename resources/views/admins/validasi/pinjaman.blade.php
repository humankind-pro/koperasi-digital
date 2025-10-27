<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Validasi Pengajuan Pinjaman</h3>
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
                    @endif

                    <div class="space-y-6">
                        @forelse ($pengajuanPinjaman as $pinjaman)
                            <div class="p-4 border rounded-lg flex flex-col md:flex-row items-start md:items-center justify-between">
                                <div>
                                    <p class="font-bold text-lg text-gray-800">{{ $pinjaman->anggota->nama ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600">Jumlah Diajukan: <span class="font-semibold">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</span></p>
                                    <p class="text-xs text-gray-500">Diajukan oleh: {{ $pinjaman->diajukanOleh->name ?? 'N/A' }}</p>
                                </div>
                                <div class="mt-4 md:mt-0 flex items-center space-x-2">
                                    <form action="{{ route('admin.validasi.pinjaman.setujui', $pinjaman->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center">
                                            <input type="number" name="jumlah_disetujui" value="{{ $pinjaman->jumlah_pinjaman }}" placeholder="Jumlah Disetujui (Rp)" class="text-sm border-gray-300 rounded-md shadow-sm" required>
                                            <button type="submit" class="ml-2 px-3 py-1 text-xs font-medium text-white bg-green-500 rounded hover:bg-green-600">Setujui</button>
                                        </div>
                                    </form>
                                    <form action="{{ route('admin.validasi.pinjaman.tolak', $pinjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pengajuan ini?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-red-500 rounded hover:bg-red-600">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">Tidak ada pengajuan pinjaman yang perlu divalidasi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>