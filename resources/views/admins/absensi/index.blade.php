<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h2 class="text-xl font-bold text-gray-800">Rekap Absensi Karyawan</h2>

                        {{-- FORM FILTER TANGGAL --}}
                        <form action="{{ route('admin.absensi.index') }}" method="GET" class="flex items-center gap-2">
                            <label for="tanggal" class="text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                            <input type="date" name="tanggal" id="tanggal" 
                                   value="{{ request('tanggal', now()->toDateString()) }}"
                                   class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-bold">
                                Filter
                            </button>
                            
                            {{-- Tombol Reset ke Hari Ini --}}
                            @if(request('tanggal'))
                                <a href="{{ route('admin.absensi.index') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- TABEL DATA --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Karyawan</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jam Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fingerprint ID</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($absensi as $absen)
                                <tr class="hover:bg-gray-50">
                                    {{-- Nama --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-2">
                                                {{ substr($absen->user->name, 0, 1) }}
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $absen->user->name }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
    {{-- Gunakan created_at --}}
    {{ \Carbon\Carbon::parse($absen->created_at)->translatedFormat('l, d F Y') }}
</td>

                                    {{-- Jam Masuk --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i:s') }}
                                    </td>

                                    {{-- Status (Telat/Tepat) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $jamMasuk = \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i:s');
                                            $batasMasuk = '08:00:00'; 
                                        @endphp

                                        @if($jamMasuk <= $batasMasuk)
                                            <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full border border-green-200">
                                                Tepat Waktu
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full border border-red-200">
                                                Terlambat
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ID Alat --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-400">
                                        #{{ $absen->user->fingerprint_id }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada data absensi pada tanggal ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $absensi->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>