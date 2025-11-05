<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Cari Riwayat Validasi Pinjaman Nasabah
                    </h3>

                    {{-- Area untuk pesan error/sukses pencarian --}}
                    <div id="search-feedback" class="mb-4 text-sm"></div>

                    {{-- Form Pencarian NIK --}}
                    <div class="mb-8 flex items-end space-x-2">
                        <div class="flex-grow">
                            <label for="nik_search" class="block text-sm font-medium text-gray-700">Masukkan NIK Nasabah</label>
                            <input type="text" id="nik_search" name="nik_search" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="NIK...">
                        </div>
                        <button type="button" id="search-button" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">
                            Cari
                        </button>
                    </div>

                    {{-- Area untuk Menampilkan Hasil (Awalnya tersembunyi) --}}
                    <div id="result-area" class="mt-8 hidden">
                        <h4 class="text-lg font-semibold mb-4">
                            Riwayat Pinjaman untuk: <span id="anggota-name" class="text-cyan-600"></span>
                            <span class="text-base font-medium text-gray-700">(Skor Kredit: <span id="anggota-skor" class="font-bold"></span>)</span>
                        </h4>

                        <div id="riwayat-list" class="space-y-3">
                            {{-- Data riwayat akan dimasukkan di sini oleh JavaScript --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL TRANSFER PINJAMAN --}}
    {{-- ======================================================= --}}
    <div id="transferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50">
      <div class="relative p-8 bg-white w-full max-w-lg mx-auto rounded-lg shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-gray-900">Transfer Pinjaman Nasabah <span id="modal-current-anggota" class="text-cyan-600"></span></h3>
          <button onclick="closeTransferModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>
        <form id="transferForm" method="POST" action=""> {{-- Action akan di-set oleh JS --}}
            @csrf
            @method('PATCH')
            <input type="hidden" name="pinjaman_id" id="modal-transfer-pinjaman-id">
            <div class="mt-2 text-sm text-gray-700 space-y-4">
                <div>
                    <label for="new_anggota_id" class="block font-medium text-gray-700">Pindahkan Ke Nasabah:</label>
                    <select name="new_anggota_id" id="new_anggota_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- Pilih Nasabah Tujuan --</option>
                        {{-- Opsi akan diisi oleh JS dari variabel 'semuaAnggotaList' --}}
                    </select>
                </div>
                 <div>
                    <label for="alasan_transfer" class="block font-medium text-gray-700">Alasan Transfer (Opsional):</label>
                    <textarea name="alasan_transfer" id="alasan_transfer" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>
            <div class="mt-6 text-right">
                 <button type="button" onclick="closeTransferModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-2">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                    Proses Transfer
                </button>
            </div>
        </form>
      </div>
    </div>
    {{-- ======================================================= --}}


    @push('scripts')
    <script>
        // ===============================================
        // Variabel dan Fungsi untuk MODAL TRANSFER
        // ===============================================
        const transferModal = document.getElementById('transferModal');
        const transferForm = document.getElementById('transferForm');
        const modalPinjamanIdInput = document.getElementById('modal-transfer-pinjaman-id');
        const modalCurrentAnggotaSpan = document.getElementById('modal-current-anggota');
        const newAnggotaSelect = document.getElementById('new_anggota_id');
        
        // Mengambil data anggota dari Controller (PHP)
        let semuaAnggotaList = @json($semuaAnggotaDisetujui ?? []);

        function openTransferModal(pinjamanId, currentAnggotaName) {
            modalPinjamanIdInput.value = pinjamanId;
            modalCurrentAnggotaSpan.textContent = currentAnggotaName;
            transferForm.action = `/admin/pinjaman/${pinjamanId}/transfer`; 

            // Isi dropdown, kecualikan anggota saat ini
            newAnggotaSelect.innerHTML = '<option value="">-- Pilih Nasabah Tujuan --</option>'; // Reset
            semuaAnggotaList.forEach(anggota => {
                 if (anggota.nama !== currentAnggotaName) {
                    const option = document.createElement('option');
                    option.value = anggota.id;
                    option.textContent = `${anggota.nama} (NIK: ${anggota.no_ktp})`;
                    newAnggotaSelect.appendChild(option);
                 }
            });

            document.getElementById('alasan_transfer').value = '';
            transferModal.classList.remove('hidden');
        }

        function closeTransferModal() {
            transferModal.classList.add('hidden');
        }
        
        // ===============================================
        // Variabel dan Fungsi untuk PENCARIAN NIK
        // ===============================================
        document.addEventListener('DOMContentLoaded', function () {
            const searchButton = document.getElementById('search-button');
            const nikInput = document.getElementById('nik_search');
            const searchFeedback = document.getElementById('search-feedback');
            const resultArea = document.getElementById('result-area');
            const anggotaNameEl = document.getElementById('anggota-name');
            const riwayatListDiv = document.getElementById('riwayat-list');
            const anggotaSkorEl = document.getElementById('anggota-skor');

            searchButton.addEventListener('click', function () {
                const nik = nikInput.value.trim();
                searchFeedback.textContent = '';
                resultArea.classList.add('hidden');
                riwayatListDiv.innerHTML = ''; // Kosongkan riwayat

                if (!nik) {
                    searchFeedback.textContent = 'Silakan masukkan NIK.';
                    searchFeedback.className = 'mb-4 text-sm text-red-600';
                    return;
                }

                searchFeedback.textContent = 'Mencari...';
                searchFeedback.className = 'mb-4 text-sm text-gray-500';

                fetch(`{{ route('admin.search.nik.riwayat') }}?nik=${nik}`, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        searchFeedback.textContent = '';
                        anggotaNameEl.textContent = data.anggota.nama;
                        
                        // Logika Skor Kredit (100 = Bagus)
                        anggotaSkorEl.textContent = data.anggota.skor_kredit;
                        if (data.anggota.skor_kredit < 70) {
                            anggotaSkorEl.className = 'font-bold text-red-500';
                        } else if (data.anggota.skor_kredit < 90) {
                            anggotaSkorEl.className = 'font-bold text-yellow-500';
                        } else {
                            anggotaSkorEl.className = 'font-bold text-green-500';
                        }

                        if (data.riwayat && data.riwayat.length > 0) {
                            data.riwayat.forEach(pinjaman => {
                        
                                const card = document.createElement('div');
                                card.className = 'bg-white shadow border border-gray-200 rounded-lg overflow-hidden';


                                const cardHeader = document.createElement('div');
                                cardHeader.className = 'p-4 cursor-pointer hover:bg-gray-50';
                                
                                const tglValidasi = pinjaman.tanggal_validasi ? new Date(pinjaman.tanggal_validasi).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                                const jmlSetuju = pinjaman.status === 'disetujui' ? `Rp ${parseInt(pinjaman.jumlah_disetujui).toLocaleString('id-ID')}` : '-';
                                
                                // Logika Status (Termasuk Lunas)
                                let statusClass = '';
                                let statusText = '';
                                if (pinjaman.status === 'disetujui') {
                                    if (pinjaman.sisa_hutang <= 0) {
                                        statusText = 'Lunas';
                                        statusClass = 'bg-blue-100 text-blue-800';
                                    } else {
                                        statusText = 'Disetujui (Aktif)';
                                        statusClass = 'bg-green-100 text-green-800';
                                    }
                                } else if (pinjaman.status === 'ditolak') {
                                    statusText = 'Ditolak';
                                    statusClass = 'bg-red-100 text-red-800';
                                } else {
                                    statusText = pinjaman.status.charAt(0).toUpperCase() + pinjaman.status.slice(1);
                                    statusClass = 'bg-gray-100 text-gray-800';
                                }
                                
                                cardHeader.innerHTML = `
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm text-gray-500">Divalidasi: ${tglValidasi}</p>
                                            <p class="font-semibold text-gray-800">Jumlah: ${jmlSetuju}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">${statusText}</span>
                                            <span class="text-xs text-gray-400 block mt-1">Klik untuk lihat riwayat bayar ▼</span>
                                        </div>
                                    </div>
                                `;


                                const cardBody = document.createElement('div');
                                cardBody.className = 'p-4 bg-gray-50 border-t border-gray-200 hidden'; // <-- 'hidden' by default
                                
                                let paymentsHTML = '<h5 class="font-semibold mb-2 text-sm text-gray-700">Riwayat Pembayaran:</h5>';
                                if (pinjaman.pembayaran && pinjaman.pembayaran.length > 0) {
                                    paymentsHTML += '<ul class="list-disc list-inside space-y-1 text-sm">';
                                    pinjaman.pembayaran.forEach(pembayaran => {
                                        const tglBayar = new Date(pembayaran.tanggal_bayar).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                                        const jmlBayar = `Rp ${parseInt(pembayaran.jumlah_bayar).toLocaleString('id-ID')}`;
                                        

                                        let buktiLink = '';
                                        if (pembayaran.bukti_transfer_path) {
                                            const imageUrl = `${window.location.origin}/storage/${pembayaran.bukti_transfer_path}`;
                                            buktiLink = ` <a href="${imageUrl}" target="_blank" class="ml-2 px-2 py-0.5 text-xs font-medium text-white bg-blue-500 rounded hover:bg-blue-600">Lihat Bukti</a>`;
                                        }

                                        paymentsHTML += `<li><strong>${tglBayar}</strong>: ${jmlBayar} ${buktiLink}</li>`;
                                    });
                                    paymentsHTML += '</ul>';
                                } else {
                                    paymentsHTML += '<p class="text-sm text-gray-500">Belum ada riwayat pembayaran.</p>';
                                }
                                cardBody.innerHTML = paymentsHTML;


                                cardHeader.addEventListener('click', () => {
                                    cardBody.classList.toggle('hidden');
                                });
                                

                                const cardFooter = document.createElement('div');
                                cardFooter.className = 'p-4 bg-gray-50 border-t border-gray-200 text-right';

                                if (pinjaman.status === 'disetujui' && pinjaman.sisa_hutang > 0) {
                                    const transferButton = document.createElement('button');
                                    transferButton.type = 'button';
                                    transferButton.className = 'px-3 py-1 text-xs font-medium text-white bg-orange-500 rounded hover:bg-orange-600';
                                    transferButton.textContent = 'Transfer Pinjaman';
                                    const currentAnggotaName = data.anggota ? data.anggota.nama : 'Nasabah Ini';
                                    transferButton.onclick = function() {
                                        openTransferModal(pinjaman.id, currentAnggotaName);
                                    };
                                    cardFooter.appendChild(transferButton);
                                }
                                
                                
                                card.appendChild(cardHeader);
                                card.appendChild(cardBody);
                                if (cardFooter.hasChildNodes()) {
                                    card.appendChild(cardFooter);
                                }
                                riwayatListDiv.appendChild(card);
                            });
                        } else {
                            riwayatListDiv.innerHTML = '<div class="text-center py-4 text-gray-500">Nasabah ini belum memiliki riwayat validasi pinjaman.</div>';
                        }
                        resultArea.classList.remove('hidden');
                    } else {
                        searchFeedback.textContent = data.message || 'Gagal mengambil data.';
                        searchFeedback.className = 'mb-4 text-sm text-red-600';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchFeedback.textContent = 'Terjadi kesalahan. Coba lagi.';
                    searchFeedback.className = 'mb-4 text-sm text-red-600';
                });
            });
        });
    </script>
    @endpush
</x-app-layout>