<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 px-6 lg:px-0">Daftar Pinjaman Aktif</h3>

            {{-- Pesan Sukses / Error --}}
            @if (session('success'))
                <div class="mb-4 mx-6 lg:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 mx-6 lg:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-6 lg:px-0">
                @forelse ($pinjamanAktif as $pinjaman)
                    {{-- 
                        LOGIKA SEDERHANA (DATABASE BASED):
                        Karena controller Anda SUDAH mengupdate 'sisa_hutang' setiap kali bayar,
                        kita cukup ambil langsung dari database. Tidak perlu hitung ulang di View.
                    --}}
                    @php
                        $totalPinjaman = $pinjaman->jumlah_disetujui; 
                        
                        // Sisa hutang diambil langsung dari kolom database 'sisa_hutang'
                        $sisaHutang    = $pinjaman->sisa_hutang; 
                        
                        // Hitung 'Sudah Dibayar' hanya untuk tampilan (Total - Sisa)
                        $sudahDibayar  = $totalPinjaman - $sisaHutang;
                        
                        // Status lunas jika sisa hutang 0 atau status 'lunas'
                        $isLunas       = $sisaHutang <= 0 || $pinjaman->status == 'lunas';
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-between border-l-4 {{ $isLunas ? 'border-green-500' : 'border-indigo-500' }}">
                        <div>
                            {{-- Header Kartu --}}
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-lg text-gray-800">{{ $pinjaman->anggota->nama ?? 'Nasabah' }}</p>
                                    <p class="text-xs text-gray-500">{{ $pinjaman->anggota->no_ktp ?? '-' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-600">
                                    Tenor: {{ $pinjaman->lama_angsuran ?? '-' }} Bln
                                </span>
                            </div>
                            
                            <hr class="my-3">

                            {{-- Total Pinjaman --}}
                            <p class="text-sm text-gray-600 flex justify-between">
                                <span>Total Pinjaman:</span> 
                                <span class="font-bold">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</span>
                            </p>

                            {{-- Sudah Dibayar --}}
                            <p class="text-sm text-gray-600 flex justify-between mt-1">
                                <span>Sudah Dibayar:</span> 
                                <span class="font-semibold text-green-600">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</span>
                            </p>

                            {{-- Sisa Hutang (Langsung dari DB) --}}
                            @if ($isLunas)
                                <div class="mt-3 text-center p-2 bg-green-100 text-green-800 rounded-md font-bold text-sm">
                                    LUNAS 🎉
                                </div>
                            @else
                                <p class="mt-2 text-sm text-red-600 flex justify-between border-t pt-2 border-dashed border-red-200">
                                    <span>Sisa Hutang:</span>
                                    <span class="font-bold text-lg">Rp {{ number_format($sisaHutang, 0, ',', '.') }}</span>
                                </p>
                            @endif
                            
                            {{-- Jatuh Tempo --}}
                            @if (!$isLunas && $pinjaman->tenggat_berikutnya)
                                <p class="mt-2 text-xs text-gray-500">
                                    Jatuh Tempo: <span class="text-red-500 font-semibold">{{ \Carbon\Carbon::parse($pinjaman->tenggat_berikutnya)->format('d M Y') }}</span>
                                </p>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-6">
                            @if (!$isLunas)
                                <button type="button"
                                    class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded shadow hover:bg-indigo-700 transition duration-150"
                                    onclick="openPaymentModal({{ $pinjaman->id }}, '{{ $pinjaman->anggota->nama }}', {{ $sisaHutang }})">
                                    💵 Bayar Cicilan
                                </button>
                            @else
                                <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 text-sm font-bold rounded cursor-not-allowed">
                                    Selesai
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 lg:col-span-3 text-center py-10 bg-white rounded-lg shadow-sm">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pinjaman aktif</h3>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 px-6 lg:px-0">
                 {{ $pinjamanAktif->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL PEMBAYARAN --}}
    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-6 bg-white w-full max-w-md m-auto rounded-lg shadow-2xl">
            <div class="flex justify-between items-center mb-5 border-b pb-2">
                <h3 class="text-xl font-bold text-gray-800">Input Pembayaran</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-red-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form id="paymentForm" method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pinjaman_id" id="modal-pinjaman-id">
                
                <div class="space-y-4">
                    <div class="bg-indigo-50 p-3 rounded text-sm">
                        <p class="text-gray-600">Nasabah: <span id="modal-anggota-nama" class="font-bold text-indigo-700"></span></p>
                        <p class="text-gray-600">Sisa Hutang: <span id="modal-sisa-hutang" class="font-bold text-red-600"></span></p>
                    </div>

                    <div>
                        <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-lg font-bold" required min="1" placeholder="0">
                    </div>

                    <div>
                        <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="w-full border-gray-300 rounded-md shadow-sm" required value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div>
                        <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer (Struk/Foto)</label>
                        <input type="file" name="bukti_transfer" id="bukti_transfer" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                     <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-bold shadow">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const paymentModal = document.getElementById('paymentModal');
        const modalPinjamanId = document.getElementById('modal-pinjaman-id');
        const modalAnggotaNama = document.getElementById('modal-anggota-nama');
        const modalSisaHutang = document.getElementById('modal-sisa-hutang');
        const inputJumlahBayar = document.getElementById('jumlah_bayar');

        function openPaymentModal(pinjamanId, anggotaNama, sisaHutang) {
            modalPinjamanId.value = pinjamanId;
            modalAnggotaNama.textContent = anggotaNama;
            modalSisaHutang.textContent = 'Rp ' + parseInt(sisaHutang).toLocaleString('id-ID');
            inputJumlahBayar.max = sisaHutang; 
            inputJumlahBayar.value = ''; 
            paymentModal.classList.remove('hidden');
        }

        function closePaymentModal() {
            paymentModal.classList.add('hidden');
        }

        window.onclick = function(event) {
            if (event.target == paymentModal) {
                closePaymentModal();
            }
        }
    </script>
</x-app-layout>