<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- HEADER & FORM PENCARIAN --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h2 class="text-xl font-bold text-gray-800">Riwayat Pinjaman Nasabah Saya</h2>
                        
                        {{-- Form Search (Method GET) --}}
                        <form method="GET" action="{{ route('karyawan.pinjaman.riwayat') }}" class="flex w-full md:w-auto gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full md:w-64" 
                                   placeholder="Cari NIK atau Nama...">
                            
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-medium">
                                Cari
                            </button>
                            
                            @if(request('search'))
                                <a href="{{ route('karyawan.pinjaman.riwayat') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 font-medium flex items-center">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- TABEL DATA --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nasabah</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa Hutang</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($riwayat as $p)
                                <tr class="hover:bg-gray-50">
                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</div>
                                    </td>
                                    
                                    {{-- Nasabah --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $p->anggota->nama }}</div>
                                        <div class="text-xs text-gray-500">NIK: {{ $p->anggota->no_ktp }}</div>
                                    </td>
                                    
                                    {{-- Jumlah & Tenor --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($p->jumlah_disetujui ?? $p->jumlah_pinjaman, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $p->lama_angsuran }} Bulan</div>
                                    </td>

                                    {{-- Sisa Hutang --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($p->status == 'disetujui')
                                            @if($p->sisa_hutang <= 0)
                                                <span class="text-green-600 font-bold text-sm">Lunas</span>
                                            @else
                                                <span class="text-red-600 font-bold text-sm">
                                                    Rp {{ number_format($p->sisa_hutang, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($p->status == 'pending') 
                                            <span class="text-yellow-700 bg-yellow-100 px-2 py-1 rounded-full text-xs font-semibold">Menunggu</span>
                                        @elseif($p->status == 'disetujui') 
                                            @if($p->sisa_hutang <= 0)
                                                <span class="text-blue-700 bg-blue-100 px-2 py-1 rounded-full text-xs font-semibold">Lunas</span>
                                            @else
                                                <span class="text-green-700 bg-green-100 px-2 py-1 rounded-full text-xs font-semibold">Aktif</span>
                                            @endif
                                        @else 
                                            <span class="text-red-700 bg-red-100 px-2 py-1 rounded-full text-xs font-semibold">Ditolak</span>
                                        @endif
                                    </td>

                                    {{-- Aksi (Tombol Riwayat Bayar) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($p->status == 'disetujui' || $p->status == 'lunas')
                                            {{-- TOMBOL INI MEMBUKA MODAL & KIRIM DATA PEMBAYARAN KE JS --}}
                                            <button onclick='openHistoryModal(@json($p->pembayaran), "{{ $p->anggota->nama }}")'
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded border border-indigo-200 shadow-sm transition hover:bg-indigo-100 flex items-center gap-1">
                                                <span>📋</span> Riwayat Bayar
                                            </button>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Tidak ada aksi</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $riwayat->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL HISTORY PEMBAYARAN --}}
    {{-- ======================================================= --}}
    <div id="historyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50">
        <div class="relative p-6 bg-white w-full max-w-2xl mx-auto rounded-lg shadow-xl">
            {{-- Header Modal --}}
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Pembayaran: <span id="history-nama" class="text-indigo-600"></span></h3>
                <button onclick="document.getElementById('historyModal').classList.add('hidden')" class="text-gray-400 hover:text-red-600 text-2xl font-bold">&times;</button>
            </div>
            
            {{-- Isi Tabel Modal --}}
            <div class="overflow-y-auto max-h-96">
                <table class="min-w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-4 py-2">Tanggal Bayar</th>
                            <th class="px-4 py-2">Jumlah</th>
                            <th class="px-4 py-2 text-center">Bukti Transfer</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body">
                        {{-- Data akan diisi lewat Javascript --}}
                    </tbody>
                </table>
                <p id="no-history-msg" class="text-center text-gray-500 py-4 hidden">Belum ada riwayat pembayaran.</p>
            </div>

            {{-- Footer Modal --}}
            <div class="mt-4 text-right">
                <button onclick="document.getElementById('historyModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-semibold">Tutup</button>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        function openHistoryModal(payments, nama) {
            const modal = document.getElementById('historyModal');
            const tbody = document.getElementById('history-table-body');
            const noMsg = document.getElementById('no-history-msg');
            
            document.getElementById('history-nama').innerText = nama;
            tbody.innerHTML = ''; // Reset tabel sebelum diisi

            if (!payments || payments.length === 0) {
                noMsg.classList.remove('hidden');
            } else {
                noMsg.classList.add('hidden');
                
                // Urutkan pembayaran dari terbaru
                payments.sort((a, b) => new Date(b.tanggal_bayar) - new Date(a.tanggal_bayar));

                // Loop Data Pembayaran
                payments.forEach(pay => {
                    const date = new Date(pay.tanggal_bayar).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    const amount = parseInt(pay.jumlah_bayar).toLocaleString('id-ID');
                    
                    // Logic Tombol Lihat Foto
                    let buktiBtn = '<span class="text-gray-400 italic text-xs">Tidak ada bukti</span>';
                    
                    if (pay.bukti_transfer_path) {
                        // Menggunakan helper asset blade yg dirender jadi string
                        const storageRoot = "{{ asset('storage') }}"; 
                        const url = `${storageRoot}/${pay.bukti_transfer_path}`;
                        
                        buktiBtn = `<a href="${url}" target="_blank" class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded hover:bg-blue-200 flex items-center justify-center gap-1">
                                        <span>📷</span> Lihat Foto
                                    </a>`;
                    }

                    const row = `
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">${date}</td>
                            <td class="px-4 py-3 text-green-600 font-bold">Rp ${amount}</td>
                            <td class="px-4 py-3 text-center">${buktiBtn}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }

            modal.classList.remove('hidden');
        }

        // Tutup modal jika klik di area gelap (overlay)
        window.onclick = function(event) {
            const modal = document.getElementById('historyModal');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>