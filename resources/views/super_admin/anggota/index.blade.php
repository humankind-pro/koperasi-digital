<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Manajemen Data Nasabah</h3>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                         <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
                    @endif

                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2 font-semibold text-xs uppercase">
                            <div class="w-1/4">Nama / No KTP</div>
                            <div class="w-1/4">Pekerjaan</div>
                            <div class="w-1/4">Diajukan Oleh</div>
                            <div class="w-1/4 text-center">Status</div>
                            <div class="w-auto text-right">Aksi</div>
                        </div>

                        @forelse ($anggotas as $anggota)
                            <div class="flex items-center py-3 border-b text-sm">
                                <div class="w-1/4">
                                    <p class="font-semibold text-gray-800">{{ $anggota->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $anggota->no_ktp }}</p>
                                </div>
                                <div class="w-1/4 text-gray-600">{{ $anggota->pekerjaan }}</div>
                                <div class="w-1/4 text-gray-600">{{ $anggota->dibuatOleh->name ?? 'N/A' }}</div>
                                <div class="w-1/4 text-center">
                                     <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($anggota->status == 'disetujui') bg-green-100 text-green-800
                                        @elseif($anggota->status == 'ditolak') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($anggota->status) }}
                                    </span>
                                </div>
                                <div class="w-auto text-right space-x-2 whitespace-nowrap">
                                     <a href="{{ route('superadmin.anggota.edit', $anggota->id) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Edit</a>
                                     <form action="{{ route('superadmin.anggota.destroy', $anggota->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus anggota ini? Ini tidak bisa dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                     </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">Belum ada data nasabah.</div>
                        @endforelse
                    </div>
                     <div class="mt-6">
                         {{ $anggotas->links() }}
                     </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>