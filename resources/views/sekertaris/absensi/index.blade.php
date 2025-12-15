<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header & Tombol Cetak --}}
            <div class="mb-6 flex flex-col md:flex-row justify-between items-center no-print">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Rekap Absensi</h2>
                    <p class="text-sm text-gray-500">Data rekap kehadiran harian (Masuk & Pulang).</p>
                </div>
                <button onclick="window.print()" class="bg-gray-800 text-white px-5 py-2 rounded-lg hover:bg-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak
                </button>
            </div>

            {{-- Form Filter --}}
            <div class="bg-white rounded-xl p-6 mb-6 shadow-sm border border-gray-100 no-print">
                <form method="GET" action="{{ route('sekertaris.absensi.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                            <select name="user_id" class="w-full rounded-lg border-gray-300">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach($karyawans as $k)
                                    <option value="{{ $k->id }}" {{ request('user_id') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2.5 rounded-lg w-full">Filter</button>
                            <a href="{{ route('sekertaris.absensi.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2.5 rounded-lg text-center">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Judul Cetak --}}
            <div class="hidden print-header mb-6 text-center">
                <h1 class="text-2xl font-bold uppercase">Laporan Absensi Karyawan</h1>
                <p class="text-sm">Periode: {{ request('start_date') ?? 'Awal' }} s/d {{ request('end_date') ?? 'Akhir' }}</p>
                <hr class="my-4 border-gray-800">
            </div>

            {{-- Tabel Data --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 print-border-none">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-800 text-white print-bg-gray">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase">Nama</th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase">Jam Masuk</th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase">Jam Pulang</th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase">Total Jam</th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($rekapAbsensi as $data)
                                @php
                                    $masuk = \Carbon\Carbon::parse($data->jam_masuk);
                                    
                                    // Logika: Jika jumlah scan > 1, berarti ada scan masuk DAN scan pulang
                                    $pulang = ($data->jumlah_scan > 1) ? \Carbon\Carbon::parse($data->jam_keluar) : null;
                                    
                                    $durasi = $pulang ? $masuk->diff($pulang)->format('%H Jam %I Menit') : '-';
                                    $isTelat = $masuk->format('H:i') > '08:00'; // Contoh jam masuk 08:00
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    {{-- Tanggal --}}
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
                                    </td>
                                    
                                    {{-- Nama --}}
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800">{{ $data->user->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500 no-print">ID: {{ $data->fingerprint_id }}</div>
                                    </td>

                                    {{-- Jam Masuk --}}
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <span class="text-sm font-mono {{ $isTelat ? 'text-red-600 font-bold' : 'text-green-600' }}">
                                            {{ $masuk->format('H:i') }}
                                        </span>
                                        @if($isTelat) <div class="text-[10px] text-red-500 font-bold">TERLAMBAT</div> @endif
                                    </td>

                                    {{-- Jam Pulang --}}
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        @if($pulang)
                                            <span class="text-sm font-mono text-blue-600 font-bold">
                                                {{ $pulang->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">-- : --</span>
                                        @endif
                                    </td>

                                    {{-- Durasi --}}
                                    <td class="px-6 py-3 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $durasi }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        @if($pulang)
                                            <span class="px-2 py-1 text-xs font-bold rounded bg-green-100 text-green-800 border border-green-200">Hadir Lengkap</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-bold rounded bg-yellow-100 text-yellow-800 border border-yellow-200">Belum Pulang</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Data absensi tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 no-print">
                    {{ $rekapAbsensi->links() }}
                </div>
            </div>

            {{-- Footer Tanda Tangan --}}
            <div class="hidden print-footer mt-12 grid grid-cols-2">
                <div class="text-center">
                    <p>Mengetahui,</p>
                    <p class="font-bold mt-16 border-b border-black inline-block px-10">Pimpinan</p>
                </div>
                <div class="text-center">
                    <p>Dibuat Oleh,</p>
                    <p class="font-bold mt-16 border-b border-black inline-block px-10">Sekertaris</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Style Cetak --}}
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-header, .print-footer { display: block !important; }
            .print-footer { display: grid !important; }
            .print-bg-gray { background-color: #f3f4f6 !important; color: black !important; -webkit-print-color-adjust: exact; }
            body { background: white; }
        }
    </style>
</x-app-layout>