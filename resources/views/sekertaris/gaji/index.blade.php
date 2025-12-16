<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Header & Tombol Tambah --}}
                    <div class="flex justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Kelola Gaji Pegawai</h3>
                        <a href="{{ route('sekertaris.gaji.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            + Input Gaji Baru
                        </a>
                    </div>

                    {{-- Pesan Sukses --}}
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
                    @endif

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Nama Pegawai</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Jabatan</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Total Gaji</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($dataGaji as $gaji)
                                    <tr>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d M Y') }}</td>
                                        <td class="px-4 py-2">{{ $gaji->user->name }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $gaji->user->role == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($gaji->user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 font-bold text-green-600">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-4">{{ $dataGaji->links() }}</div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>