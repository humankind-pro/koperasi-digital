<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Manajemen Data Karyawan
                    </h3>

                    <a href="{{ route('karyawans.create') }}" class="inline-block mb-6 px-4 py-2 bg-cyan-500 border border-transparent rounded-md text-sm text-white hover:bg-cyan-600 transition ease-in-out duration-150">
                        Tambahkan Karyawan Baru
                    </a>

                    {{-- Container untuk list data --}}
                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2">
                            <div class="w-1/3">
                                <span class="text-xs font-semibold text-gray-500 uppercase">Nama</span>
                            </div>
                            <div class="w-1/3">
                                <span class="text-xs font-semibold text-gray-500 uppercase">Email</span>
                            </div>
                        </div>

                        @forelse ($karyawans as $karyawan)
                            <div class="flex items-center py-3 border-b">
                                <div class="w-1/3">
                                    <p class="font-semibold text-gray-800">{{ $karyawan->name }}</p>
                                </div>
                                <div class="w-1/3">
                                    <p class="text-sm text-gray-600">{{ $karyawan->email }}</p>
                                </div>
                                <div class="w-1/3 text-right">
                                    <a href="{{ route('karyawans.edit', $karyawan->id) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Edit</a>
                                    <form action="{{ route('karyawans.destroy', $karyawan->id) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Belum ada data karyawan.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>