<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Validasi Pengajuan Anggota Baru
                    </h3>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2">
                            <div class="w-1/3"><span class="text-xs font-semibold text-gray-500 uppercase">Nama Anggota</span></div>
                            <div class="w-1/3"><span class="text-xs font-semibold text-gray-500 uppercase">Diajukan Oleh (Karyawan)</span></div>
                            <div class="w-1/3 text-right"><span class="text-xs font-semibold text-gray-500 uppercase">Aksi</span></div>
                        </div>

                        @forelse ($pengajuanAnggota as $anggota)
                            <div class="flex items-center py-3 border-b">
                                <div class="w-1/3">
                                    <p class="font-semibold text-gray-800">{{ $anggota->nama }}</p>
                                    <p class="text-sm text-gray-500">{{ $anggota->no_ktp }}</p>
                                </div>
                                <div class="w-1/3">
                                    <p class="text-sm text-gray-600">{{ $anggota->dibuatOleh->name ?? 'N/A' }}</p>
                                </div>
                                <div class="w-1/3 text-right space-x-2">

                                    {{-- =============================================================== --}}
                                    {{-- PERBAIKAN: Nama route diubah menjadi 'admin.validasi.nasabah.tolak' --}}
                                    {{-- =============================================================== --}}
                                    <form action="{{ route('admin.validasi.nasabah.tolak', $anggota->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-red-500 rounded hover:bg-red-600">
                                            Tolak
                                        </button>
                                    </form>

                                    {{-- =============================================================== --}}
                                    {{-- PERBAIKAN: Nama route diubah menjadi 'admin.validasi.nasabah.setujui' --}}
                                    {{-- =============================================================== --}}
                                    <form action="{{ route('admin.validasi.nasabah.setujui', $anggota->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-green-500 rounded hover:bg-green-600">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Tidak ada pengajuan anggota baru yang perlu divalidasi.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>