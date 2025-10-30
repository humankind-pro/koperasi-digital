<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Riwayat Absensi Anda
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2 font-semibold text-gray-500 uppercase text-xs">
                            <div class="w-1/3">Tanggal</div>
                            <div class="w-1/3 text-center">Jam Masuk</div>
                            <div class="w-1/3 text-center">Jam Pulang</div>
                        </div>

                        @forelse ($riwayatAbsensi as $absensi)
                            <div class="flex items-center py-4 border-b">
                                <div class="w-1/3">
                                    <p class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($absensi->tanggal)->format('l, d F Y') }}
                                    </p>
                                </div>
                                <div class="w-1/3 text-center">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i:s') }}
                                    </span>
                                </div>
                                <div class="w-1/3 text-center">
                                    @if ($absensi->jam_masuk != $absensi->jam_pulang)
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i:s') }}
                                        </span>
                                    @else
                                        {{-- Tampilkan ini jika Karyawan lupa absen pulang --}}
                                        <span class="text-xs text-gray-500 italic">Belum absen pulang</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Belum ada riwayat absensi.
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-6">
                        {{ $riwayatAbsensi->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>