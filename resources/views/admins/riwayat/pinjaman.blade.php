<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- HEADER & FORM PENCARIAN --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-xl font-bold text-gray-800">
                            Riwayat Validasi & Pembayaran
                        </h3>

                        <form method="GET" action="{{ route('admin.riwayat.pinjaman') }}" class="flex w-full md:w-auto gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full md:w-64" 
                                   placeholder="Cari NIK atau Nama...">
                            
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-medium">
                                Cari
                            </button>

                            @if(request('search'))
                                <a href="{{ route('admin.riwayat.pinjaman') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 font-medium flex items-center">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- TABEL DATA UTAMA --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Validasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nasabah</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pinjaman</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa Hutang</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($riwayatValidasi as $pinjaman)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($pinjaman->tanggal_validasi)->format('d M Y') }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $pinjaman->anggota->nama }}</div>
                                            <div class="text-xs text-gray-500">{{ $pinjaman->anggota->no_ktp }}</div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                Rp {{ number_format($pinjaman->jumlah_disetujui, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $pinjaman->lama_angsuran }} Bulan</div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($pinjaman->sisa_hutang <= 0)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                            @else
                                                <span class="text-sm font-bold text-red-600">
                                                    Rp {{ number_format($pinjaman->sisa_hutang, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            {{-- TOMBOL LIHAT HISTORY PEMBAYARAN --}}
                                            {{-- Kita kirim data JSON ke fungsi JS lewat atribut onclick --}}
                                            <button onclick='openHistoryModal(@json($pinjaman->pembayaran), "{{ $pinjaman->anggota->nama }}")'
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded border border-blue-200">
                                                Riwayat Bayar
                                            </button>

                                            @if ($pinjaman->status == 'disetujui' && $pinjaman->sisa_hutang > 0)
                                                <button onclick="openTransferModal({{ $pinjaman->id }}, '{{ $pinjaman->anggota->nama }}')" 
                                                    class="text-orange-600 hover:text-orange-900 bg-orange-50 px-3 py-1 rounded border border-orange-200">
                                                    Transfer
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            Data tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $riwayatValidasi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL RIWAYAT PEMBAYARAN (BARU) --}}
    {{-- ======================================================= --}}
    <div id="historyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50">
        <div class="relative p-6 bg-white w-full max-w-2xl mx-auto rounded-lg shadow-xl">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Pembayaran: <span id="history-nama" class="text-blue-600"></span></h3>
                <button onclick="document.getElementById('historyModal').classList.add('hidden')" class="text-gray-400 hover:text-red-600 text-2xl font-bold">&times;</button>
            </div>
            
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
                        {{-- Data akan diisi oleh Javascript --}}
                    </tbody>
                </table>
                <p id="no-history-msg" class="text-center text-gray-500 py-4 hidden">Belum ada riwayat pembayaran.</p>
            </div>

            <div class="mt-4 text-right">
                <button onclick="document.getElementById('historyModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    {{-- MODAL TRANSFER (TETAP ADA) --}}
    <div id="transferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50">
        <div class="relative p-8 bg-white w-full max-w-lg mx-auto rounded-lg shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Transfer Pinjaman: <span id="modal-current-anggota" class="text-cyan-600"></span></h3>
                <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form id="transferForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="mt-2 text-sm text-gray-700 space-y-4">
                    <div>
                        <label for="new_anggota_id" class="block font-medium text-gray-700">Pindahkan Ke Nasabah:</label>
                        <select name="new_anggota_id" id="new_anggota_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Nasabah Tujuan --</option>
                            @foreach($semuaAnggotaDisetujui as $anggota)
                                <option value="{{ $anggota->id }}">{{ $anggota->nama }} (NIK: {{ $anggota->no_ktp }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="alasan_transfer" class="block font-medium text-gray-700">Alasan:</label>
                        <textarea name="alasan_transfer" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button type="button" onclick="document.getElementById('transferModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-md mr-2">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">Proses</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        // === FUNGSI MODAL HISTORY PEMBAYARAN ===
        function openHistoryModal(payments, nama) {
            const modal = document.getElementById('historyModal');
            const tbody = document.getElementById('history-table-body');
            const noMsg = document.getElementById('no-history-msg');
            
            document.getElementById('history-nama').innerText = nama;
            tbody.innerHTML = ''; // Bersihkan isi lama

            if (payments.length === 0) {
                noMsg.classList.remove('hidden');
            } else {
                noMsg.classList.add('hidden');
                
                // Loop data pembayaran
                payments.forEach(pay => {
                    const date = new Date(pay.tanggal_bayar).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    const amount = parseInt(pay.jumlah_bayar).toLocaleString('id-ID');
                    
                    // Cek Bukti Gambar
                    let buktiBtn = '<span class="text-gray-400 italic text-xs">Tidak ada bukti</span>';
                    if (pay.bukti_transfer_path) {
                        const url = `{{ asset('storage') }}/${pay.bukti_transfer_path}`;
                        buktiBtn = `<a href="${url}" target="_blank" class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded hover:bg-indigo-200">Lihat Foto 📷</a>`;
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

        // === FUNGSI MODAL TRANSFER ===
        function openTransferModal(id, nama) {
            const form = document.getElementById('transferForm');
            const modalName = document.getElementById('modal-current-anggota');
            const modal = document.getElementById('transferModal');

            form.action = `/admin/pinjaman/${id}/transfer`;
            modalName.textContent = nama;
            modal.classList.remove('hidden');
        }

        // TUTUP MODAL SAAT KLIK DILUAR
        window.onclick = function(event) {
            const historyModal = document.getElementById('historyModal');
            const transferModal = document.getElementById('transferModal');
            if (event.target == historyModal) historyModal.classList.add('hidden');
            if (event.target == transferModal) transferModal.classList.add('hidden');
        }
    </script>
</x-app-layout>