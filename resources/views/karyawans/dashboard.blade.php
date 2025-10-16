<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="#" class="block p-6 bg-cyan-500 text-white rounded-lg shadow hover:bg-cyan-600 transition">
                    <h5 class="text-2xl font-bold tracking-tight">Tambah Anggota Baru</h5>
                    <p class="font-normal text-cyan-100">Daftarkan nasabah baru ke dalam sistem.</p>
                </a>
                <a href="{{ route('pinjaman.create') }}" class="block p-6 bg-teal-500 text-white rounded-lg shadow hover:bg-teal-600 transition">
                    <h5 class="text-2xl font-bold tracking-tight">Ajukan Pinjaman</h5>
                    <p class="font-normal text-teal-100">Buat pengajuan pinjaman untuk anggota terdaftar.</p>
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Aktivitas Pengajuan Terakhir Anda
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2">
                            <div class="w-2/5"><span class="text-xs font-semibold text-gray-500 uppercase">Nama Anggota</span></div>
                            <div class="w-1/5"><span class="text-xs font-semibold text-gray-500 uppercase">Jumlah</span></div>
                            <div class="w-1/5"><span class="text-xs font-semibold text-gray-500 uppercase">Tanggal</span></div>
                            <div class="w-1/5"><span class="text-xs font-semibold text-gray-500 uppercase">Status</span></div>
                        </div>

                        @forelse ($pinjamanTerbaru as $pinjaman)
                            <div class="flex items-center py-3 border-b">
                                <div class="w-2/5"><p class="font-semibold text-gray-800">{{ $pinjaman->anggota->nama }}</p></div>
                                <div class="w-1/5"><p class="text-sm text-gray-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p></div>
                                <div class="w-1/5"><p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d M Y') }}</p></div>
                                <div class="w-1/5">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($pinjaman->status == 'disetujui') bg-green-100 text-green-800 
                                        @elseif($pinjaman->status == 'ditolak') bg-red-100 text-red-800 
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($pinjaman->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Anda belum pernah mengajukan pinjaman.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>