<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Riwayat Gaji Saya</h3>

                    <div class="space-y-4">
                        @forelse ($riwayatGaji as $gaji)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-150">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2">
                                    <div>
                                        <p class="font-bold text-lg text-gray-800">{{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('F Y') }}</p>
                                        <p class="text-sm text-gray-500">Diterima pada: {{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d M Y') }}</p>
                                    </div>
                                    <div class="mt-2 md:mt-0 text-right">
                                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                
                                <hr class="my-3 border-gray-200">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    {{-- Kolom Pendapatan --}}
                                    <div class="bg-green-50 p-3 rounded-md">
                                        <p class="font-semibold text-green-800 mb-2">Pendapatan</p>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Gaji Pokok:</span>
                                            <span class="font-medium">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</span>
                                        </div>
                                        @if($gaji->tunjangan > 0)
                                        <div class="flex justify-between mt-1">
                                            <span class="text-gray-600">Tunjangan:</span>
                                            <span class="font-medium">Rp {{ number_format($gaji->tunjangan, 0, ',', '.') }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Kolom Potongan --}}
                                    <div class="bg-red-50 p-3 rounded-md">
                                        <p class="font-semibold text-red-800 mb-2">Potongan</p>
                                        
                                        @if($gaji->nominal_potongan_alpa > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Alpa ({{ $gaji->jumlah_alpa }} hari):</span>
                                            <span class="font-medium text-red-600">- Rp {{ number_format($gaji->nominal_potongan_alpa, 0, ',', '.') }}</span>
                                        </div>
                                        @endif

                                        @if($gaji->nominal_potongan_terlambat > 0)
                                        <div class="flex justify-between mt-1">
                                            <span class="text-gray-600">Terlambat ({{ $gaji->jumlah_terlambat }}x):</span>
                                            <span class="font-medium text-red-600">- Rp {{ number_format($gaji->nominal_potongan_terlambat, 0, ',', '.') }}</span>
                                        </div>
                                        @endif

                                        @if($gaji->potongan > 0)
                                        <div class="flex justify-between mt-1">
                                            <span class="text-gray-600">Lain-lain:</span>
                                            <span class="font-medium text-red-600">- Rp {{ number_format($gaji->potongan, 0, ',', '.') }}</span>
                                        </div>
                                        @endif

                                        @if($gaji->nominal_potongan_alpa == 0 && $gaji->nominal_potongan_terlambat == 0 && $gaji->potongan == 0)
                                            <span class="text-gray-400 italic">Tidak ada potongan</span>
                                        @endif
                                    </div>
                                </div>

                                @if($gaji->catatan)
                                    <div class="mt-3 text-xs text-gray-500 bg-gray-100 p-2 rounded italic">
                                        Catatan: "{{ $gaji->catatan }}"
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">Belum ada riwayat gaji.</div>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $riwayatGaji->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>