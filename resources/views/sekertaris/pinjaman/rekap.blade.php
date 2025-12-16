<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- HEADER KHUSUS CETAK (Hanya muncul di kertas) --}}
                    <div class="hidden print-header text-center mb-6">
                        <h2 class="text-2xl font-bold uppercase">Koperasi Simpan Pinjam</h2>
                        <h3 class="text-xl font-semibold">Laporan Rekapitulasi Pinjaman</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Periode: {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}
                        </p>
                        <hr class="border-gray-800 my-4 border-2">
                    </div>

                    {{-- HEADER WEBSITE (Akan hilang saat diprint) --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 no-print">
                        <h3 class="text-xl font-bold text-gray-800">Rekapitulasi Pinjaman Bulanan</h3>
                        
                        {{-- Form Filter --}}
                        <form method="GET" action="{{ route('sekertaris.pinjaman.rekap') }}" class="flex space-x-2 mt-4 md:mt-0">
                            <select name="bulan" class="border-gray-300 rounded-md shadow-sm text-sm">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == $bulan ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                            <select name="tahun" class="border-gray-300 rounded-md shadow-sm text-sm">
                                @for ($i = date('Y'); $i >= date('Y')-5; $i--)
                                    <option value="{{ $i }}" {{ $i == $tahun ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">Filter</button>
                        </form>
                    </div>

                    {{-- TABEL REKAP --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-400 print-table">
                            <thead class="bg-gray-100 print-bg-gray">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Tgl Pengajuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase border border-gray-300">Nasabah</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase border border-gray-300">Jumlah Ajuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase border border-gray-300">Jumlah Disetujui</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase border border-gray-300">Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white text-sm">
                                @forelse ($dataPinjaman as $pinjaman)
                                    <tr>
                                        <td class="px-4 py-2 text-center border border-gray-300">
                                            {{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-2 font-medium border border-gray-300">
                                            {{ $pinjaman->anggota->nama }}
                                        </td>
                                        <td class="px-4 py-2 text-right border border-gray-300">
                                            Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2 text-right border border-gray-300 font-semibold">
                                            @if(in_array($pinjaman->status, ['disetujui', 'lunas']))
                                                Rp {{ number_format($pinjaman->jumlah_disetujui, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center border border-gray-300">
                                            <span class="px-2 py-1 text-xs font-bold rounded-full border 
                                                {{ $pinjaman->status == 'disetujui' ? 'bg-green-100 text-green-800 border-green-200' : 
                                                  ($pinjaman->status == 'ditolak' ? 'bg-red-100 text-red-800 border-red-200' : 
                                                  ($pinjaman->status == 'lunas' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200')) }}">
                                                {{ ucfirst($pinjaman->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-600 border border-gray-300">
                                            <div><span class="font-bold">Inp:</span> {{ explode(' ', $pinjaman->diajukanOleh->name)[0] }}</div>
                                            @if($pinjaman->divalidasiOleh)
                                            <div><span class="font-bold">Val:</span> {{ explode(' ', $pinjaman->divalidasiOleh->name)[0] }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 border border-gray-300">
                                            Tidak ada data pinjaman pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold print-bg-gray">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-gray-800 border border-gray-300 uppercase">Total Disetujui & Lunas:</td>
                                    <td class="px-4 py-3 text-right text-gray-900 border border-gray-300 text-base">
                                        Rp {{ number_format($totalNominal, 0, ',', '.') }}
                                    </td>
                                    <td colspan="2" class="border border-gray-300"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- AREA TANDA TANGAN (Muncul saat Print) --}}
                    <div class="hidden print-footer mt-12 grid grid-cols-2 gap-10">
                        <div class="text-center">
                            <p class="mb-16">Mengetahui,<br>Ketua Koperasi</p>
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

    {{-- STYLE CSS KHUSUS PRINT --}}
    <style>
        @media print {
            /* 1. Sembunyikan elemen website yang tidak perlu */
            .no-print, nav, header, footer, .sidebar, .min-h-screen > div:first-child { 
                display: none !important; 
            }
            
            /* 2. Reset Background & Margin */
            body { 
                background: white !important; 
                margin: 0; 
                padding: 0;
                font-size: 12px; /* Font agak kecil agar muat */
                -webkit-print-color-adjust: exact !important; /* Agar warna background tabel tercetak */
                print-color-adjust: exact !important;
            }

            /* 3. Tampilkan Header & Footer Laporan */
            .print-header, .print-footer { 
                display: block !important; 
            }
            .print-footer {
                display: grid !important;
            }

            /* 4. Atur Layout Kertas Landscape */
            @page { 
                size: landscape; 
                margin: 10mm; 
            }

            /* 5. Styling Tabel Print */
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
                background-color: #f3f4f6 !important; /* Warna abu-abu header tabel */
            }

            /* 6. Pastikan konten full width */
            .max-w-7xl { max-width: 100% !important; padding: 0 !important; }
            .shadow-sm { box-shadow: none !important; }
            .rounded-lg { border-radius: 0 !important; }
        }
    </style>
</x-app-layout>