<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
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

                    {{-- Tabel Rekap --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tgl Pengajuan</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Nasabah</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Jumlah Ajuan</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Jumlah Disetujui</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                @forelse ($dataPinjaman as $pinjaman)
                                    <tr>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $pinjaman->anggota->nama }}</td>
                                        <td class="px-4 py-2">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2">
                                            @if($pinjaman->status == 'disetujui')
                                                Rp {{ number_format($pinjaman->jumlah_disetujui, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $pinjaman->status == 'disetujui' ? 'bg-green-100 text-green-800' : ($pinjaman->status == 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($pinjaman->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-500">
                                            Input: {{ $pinjaman->diajukanOleh->name }}<br>
                                            Valid: {{ $pinjaman->divalidasiOleh->name ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data pinjaman pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-right">TOTAL:</td>
                                    <td class="px-4 py-2">{{ $totalPengajuan }} Pengajuan</td>
                                    <td class="px-4 py-2">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                            Cetak Laporan
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>