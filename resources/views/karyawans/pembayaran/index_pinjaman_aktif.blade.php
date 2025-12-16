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
                        LOGIKA PHP DI DALAM VIEW
                        -------------------------
                        Kita lakukan normalisasi data di sini agar tampilan UI konsisten
                        tanpa terpengaruh format penulisan di database.
                    --}}
                    @php
                        // 1. Ambil Data Angka
                        $totalPinjaman = $pinjaman->jumlah_disetujui; 
                        $sisaHutang    = $pinjaman->sisa_hutang; 
                        $sudahDibayar  = $totalPinjaman - $sisaHutang;
                        
                        // 2. Normalisasi Status (Ubah ke huruf kecil semua)
                        // Ini SOLUSI untuk masalah status tidak berubah warna
                        $statusRaw = $pinjaman->status; 
                        $status    = strtolower($statusRaw); 

                        // 3. Cek Status Lunas
                        $isLunas = $sisaHutang <= 0 || $status == 'lunas';

                        // 4. Tentukan Warna Border Berdasarkan Status
                        $borderColor = 'border-indigo-500'; // Default: Biru (Lancar)
                        
                        if ($isLunas) {
                            $borderColor = 'border-green-500'; // Hijau (Lunas)
                        } elseif ($status == 'menunggak') {
                            $borderColor = 'border-red-600';   // Merah (Menunggak)
                        }
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-between border-l-4 {{ $borderColor }} relative transition hover:shadow-md">
                        
                        {{-- BADGE STATUS MENUNGGAK --}}
                        @if($status == 'menunggak')
                            <div class="absolute top-4 right-4 bg-red-100 text-red-700 text-[10px] font-extrabold px-3 py-1 rounded-full animate-pulse border border-red-200 uppercase tracking-wide">
                                ⚠️ MENUNGGAK
                            </div>
                        @endif

                        <div>
                            {{-- Header Kartu (Info Nasabah) --}}
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="font-bold text-lg text-gray-800 line-clamp-1" title="{{ $pinjaman->anggota->nama ?? 'Nasabah' }}">
                                        {{ $pinjaman->anggota->nama ?? 'Nasabah' }}
                                    </p>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">
                                        ID: {{ $pinjaman->anggota->no_ktp ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Info Tenor --}}
                            <div class="mb-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                    Tenor: {{ $pinjaman->lama_angsuran ?? '-' }} Bulan
                                </span>
                            </div>
                            
                            <hr class="my-3 border-dashed border-gray-200">

                            {{-- Rincian Biaya --}}
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600 flex justify-between items-center">
                                    <span>Total Pinjaman</span> 
                                    <span class="font-bold text-gray-800">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</span>
                                </p>

                                <p class="text-sm text-gray-600 flex justify-between items-center">
                                    <span>Sudah Dibayar</span> 
                                    <span class="font-semibold text-green-600">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</span>
                                </p>
                            </div>

                            {{-- Tampilan Sisa Hutang Besar --}}
                            @if ($isLunas)
                                <div class="mt-5 text-center p-2.5 bg-green-50 text-green-700 rounded-lg font-bold text-sm border border-green-200 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    LUNAS
                                </div>
                            @else
                                <div class="mt-5 pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1 uppercase tracking-wider font-semibold">Sisa Kewajiban</p>
                                    <p class="text-2xl font-extrabold {{ $status == 'menunggak' ? 'text-red-600' : 'text-indigo-700' }}">
                                        Rp {{ number_format($sisaHutang, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endif
                            
                            {{-- Info Jatuh Tempo --}}
                            @if (!$isLunas && $pinjaman->tenggat_berikutnya)
                                <div class="mt-3 flex items-center {{ $status == 'menunggak' ? 'bg-red-50 p-2 rounded-md' : '' }}">
                                    <svg class="w-4 h-4 mr-1.5 {{ $status == 'menunggak' ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm-3-7h10M7 10v5"></path></svg>
                                    <p class="text-xs text-gray-500">
                                        Jatuh Tempo: 
                                        <span class="{{ $status == 'menunggak' ? 'text-red-600 font-bold' : 'text-gray-700 font-semibold' }}">
                                            {{ \Carbon\Carbon::parse($pinjaman->tenggat_berikutnya)->format('d M Y') }}
                                        </span>
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Aksi (Sticky Bottom) --}}
                        <div class="mt-6 pt-2">
                            @if (!$isLunas)
                                <button type="button"
                                    class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-bold rounded-lg shadow-sm hover:shadow transition duration-200 flex justify-center items-center gap-2 group"
                                    onclick="openPaymentModal({{ $pinjaman->id }}, '{{ addslashes($pinjaman->anggota->nama ?? 'Nasabah') }}', {{ $sisaHutang }})">
                                    <svg class="w-5 h-5 text-indigo-200 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Bayar Cicilan
                                </button>
                            @else
                                <button disabled class="w-full py-2.5 px-4 bg-gray-100 text-gray-400 text-sm font-bold rounded-lg border border-gray-200 cursor-not-allowed flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Pinjaman Selesai
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 lg:col-span-3 text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Tidak ada data pinjaman</h3>
                        <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">Saat ini belum ada data pinjaman aktif atau menunggak yang perlu ditangani.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
             <div class="mt-8 px-6 lg:px-0">
                 {{ $pinjamanAktif->links() }}
             </div>
        </div>
    </div>

    {{-- =============================================== --}}
    {{-- MODAL PEMBAYARAN (POPUP) --}}
    {{-- =============================================== --}}
    <div id="paymentModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center backdrop-blur-sm transition-all duration-300 opacity-0" aria-hidden="true">
        
        <div class="relative bg-white w-full max-w-md m-auto rounded-xl shadow-2xl transform scale-95 transition-all duration-300" id="modalContent">
            
            {{-- Header Modal --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Input Pembayaran</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="paymentForm" method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                <input type="hidden" name="pinjaman_id" id="modal-pinjaman-id">
                
                <div class="space-y-5">
                    {{-- Info Card dalam Modal --}}
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <span class="text-indigo-600 font-medium">Nasabah</span>
                            <span id="modal-anggota-nama" class="font-bold text-gray-800 truncate max-w-[150px]"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-indigo-600 font-medium">Sisa Hutang</span>
                            <span id="modal-sisa-hutang" class="font-bold text-red-600 text-lg"></span>
                        </div>
                    </div>

                    {{-- Input Jumlah Bayar --}}
                    <div>
                        <label for="jumlah_bayar" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Bayar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-gray-800 transition-all placeholder-gray-300" required min="1" placeholder="0">
                        </div>
                    </div>

                    {{-- Input Tanggal --}}
                    <div>
                        <label for="tanggal_bayar" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-gray-700" required value="{{ date('Y-m-d') }}">
                    </div>
                    
                    {{-- Input Bukti Transfer --}}
                    <div>
                        <label for="bukti_transfer" class="block text-sm font-semibold text-gray-700 mb-1">Bukti Transfer</label>
                        <div class="relative border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors">
                            <input type="file" name="bukti_transfer" id="bukti_transfer" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer focus:outline-none">
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5 ml-1">Format: JPG, PNG, WEBP (Max 2MB)</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                     <button type="button" onclick="closePaymentModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold shadow-md hover:shadow-lg transition-all flex items-center transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        const paymentModal = document.getElementById('paymentModal');
        const modalContent = document.getElementById('modalContent');
        const modalPinjamanId = document.getElementById('modal-pinjaman-id');
        const modalAnggotaNama = document.getElementById('modal-anggota-nama');
        const modalSisaHutang = document.getElementById('modal-sisa-hutang');
        const inputJumlahBayar = document.getElementById('jumlah_bayar');

        function openPaymentModal(pinjamanId, anggotaNama, sisaHutang) {
            // Isi data ke dalam modal
            modalPinjamanId.value = pinjamanId;
            modalAnggotaNama.textContent = anggotaNama;
            modalSisaHutang.textContent = 'Rp ' + parseInt(sisaHutang).toLocaleString('id-ID');
            
            // Set max pembayaran
            inputJumlahBayar.max = sisaHutang; 
            inputJumlahBayar.value = ''; 
            
            // Animasi Masuk
            paymentModal.classList.remove('hidden');
            // Sedikit delay agar class hidden hilang dulu sebelum animasi opacity
            requestAnimationFrame(() => {
                paymentModal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            });
        }

        function closePaymentModal() {
            // Animasi Keluar
            paymentModal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            // Tunggu animasi selesai baru hidden
            setTimeout(() => {
                paymentModal.classList.add('hidden');
            }, 300); 
        }

        // Tutup modal jika klik di luar area putih
        window.onclick = function(event) {
            if (event.target == paymentModal) {
                closePaymentModal();
            }
        }
    </script>
</x-app-layout>