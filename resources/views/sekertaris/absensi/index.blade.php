<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- HEADER KHUSUS CETAK --}}
                    <div class="hidden print-header text-center mb-6">
                        <h2 class="text-2xl font-bold uppercase">Koperasi Simpan Pinjam</h2>
                        <h3 class="text-xl font-semibold">Laporan Rekapitulasi Absensi</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Awal' }} 
                            s/d 
                            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Akhir' }}
                        </p>
                        <hr class="border-gray-800 my-4 border-2">
                    </div>

                    {{-- HEADER & FILTER (HILANG SAAT PRINT) --}}
                    <div class="mb-6 flex flex-col md:flex-row justify-between items-end no-print">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Laporan Rekap Absensi</h2>
                            <p class="text-sm text-gray-500">Data rekap kehadiran harian (Masuk & Pulang).</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 mb-6 shadow-sm border border-gray-200 no-print">
                        <form method="GET" action="{{ route('sekertaris.absensi.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                                    <select name="user_id" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="">-- Semua Karyawan --</option>
                                        @foreach($karyawans as $k)
                                            <option value="{{ $k->id }}" {{ request('user_id') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm w-full hover:bg-gray-700">Filter</button>
                                    <a href="{{ route('sekertaris.absensi.index') }}" class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg text-center text-sm hover:bg-gray-50">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- TABEL DATA --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-400 print-table">
                            <thead class="bg-gray-100 print-bg-gray">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase border border-gray-300">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase border border-gray-300">Nama Karyawan</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Jam Masuk</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Jam Pulang</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Durasi</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white text-sm">
                                @forelse($rekapAbsensi as $data)
                                    @php
                                        $masuk = \Carbon\Carbon::parse($data->jam_masuk);
                                        // Logika: Jika jumlah scan > 1, berarti ada scan masuk DAN scan pulang
                                        $pulang = ($data->jumlah_scan > 1) ? \Carbon\Carbon::parse($data->jam_keluar) : null;
                                        
                                        $durasi = $pulang ? $masuk->diff($pulang)->format('%H Jam %I Menit') : '-';
                                        $isTelat = $masuk->format('H:i') > '08:00'; 
                                    @endphp
                                    <tr>
                                        {{-- Tanggal --}}
                                        <td class="px-4 py-2 whitespace-nowrap border border-gray-300">
                                            {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
                                        </td>
                                        
                                        {{-- Nama --}}
                                        <td class="px-4 py-2 border border-gray-300">
                                            <div class="font-bold text-gray-800">{{ $data->user->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500 no-print">ID: {{ $data->fingerprint_id }}</div>
                                        </td>

                                        {{-- Jam Masuk --}}
                                        <td class="px-4 py-2 text-center border border-gray-300">
                                            <span class="font-mono font-bold {{ $isTelat ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $masuk->format('H:i') }}
                                            </span>
                                            @if($isTelat) <div class="text-[10px] text-red-600 font-bold uppercase mt-1">Terlambat</div> @endif
                                        </td>

                                        {{-- Jam Pulang --}}
                                        <td class="px-4 py-2 text-center border border-gray-300">
                                            @if($pulang)
                                                <span class="font-mono font-bold text-blue-600">{{ $pulang->format('H:i') }}</span>
                                            @else
                                                <span class="text-gray-400 italic">-- : --</span>
                                            @endif
                                        </td>

                                        {{-- Durasi --}}
                                        <td class="px-4 py-2 text-center text-gray-600 border border-gray-300">
                                            {{ $durasi }}
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-4 py-2 text-center border border-gray-300">
                                            @if($pulang)
                                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">Hadir Lengkap</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Belum Pulang</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic border border-gray-300">Data absensi tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination (Hilang saat Print) --}}
                    <div class="mt-4 no-print">
                        {{ $rekapAbsensi->links() }}
                    </div>

                    {{-- FOOTER TANDA TANGAN (Muncul saat Print) --}}
                    <div class="hidden print-footer mt-12 grid grid-cols-2 gap-10">
                        <div class="text-center">
                            <p class="mb-16">Mengetahui,<br>Pimpinan</p>
                            <p class="font-bold underline">_________________________</p>
                        </div>
                        <div class="text-center">
                            <p class="mb-16">Dibuat Oleh,<br>Sekertaris</p>
                            <p class="font-bold underline">{{ Auth::user()->name }}</p>
                        </div>
                    </div>

                    {{-- TOMBOL CETAK (Hilang saat Print) --}}
                    <div class="mt-6 text-right no-print">
                        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 shadow flex items-center gap-2 ml-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak Laporan
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- CSS KHUSUS PRINT --}}
    <style>
        @media print {
            /* 1. Sembunyikan elemen website */
            .no-print, nav, header, footer, .sidebar, .min-h-screen > div:first-child { 
                display: none !important; 
            }
            
            /* 2. Reset Layout */
            body { 
                background: white !important; 
                margin: 0; 
                padding: 0;
                font-size: 12px;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 3. Tampilkan Header & Footer Laporan */
            .print-header, .print-footer { 
                display: block !important; 
            }
            .print-footer {
                display: grid !important;
            }

            /* 4. Layout Kertas */
            @page { 
                size: landscape; 
                margin: 10mm; 
            }

            /* 5. Styling Tabel */
            .print-table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000 !important;
            }
            .print-table th, .print-table td {
                border: 1px solid #000 !important;
                padding: 6px 8px;
            }
            .print-bg-gray {
                background-color: #f3f4f6 !important;
            }

            /* 6. Pastikan konten full width */
            .max-w-7xl { max-width: 100% !important; padding: 0 !important; }
            .shadow-sm { box-shadow: none !important; }
            .rounded-lg, .rounded-xl { border-radius: 0 !important; border: none !important; }
        }
    </style>
</x-app-layout>