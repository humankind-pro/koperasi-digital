<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Cari Riwayat Pinjaman Nasabah</h3>

                    {{-- Area untuk pesan error/sukses --}}
                    <div id="search-feedback" class="mb-4 text-sm"></div>

                    {{-- Form Pencarian NIK --}}
                    <div class="mb-6 flex items-end space-x-2">
                        <div class="flex-grow">
                            <label for="nik_search" class="block text-sm font-medium text-gray-700">Masukkan NIK Nasabah</label>
                            <input type="text" id="nik_search" name="nik_search" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="NIK...">
                        </div>
                        <button type="button" id="search-button" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">
                            Cari
                        </button>
                    </div>

                    {{-- Area untuk Menampilkan Hasil --}}
                    <div id="result-area" class="mt-8 hidden">
                        <h4 class="text-lg font-semibold mb-2">Hasil Pencarian untuk: <span id="anggota-name" class="text-cyan-600"></span></h4>
                        
                        <div class="space-y-4">
                             <div class="flex items-center pb-2 border-b-2 font-semibold text-gray-500 uppercase text-xs">
                                <div class="w-1/4">Tgl Pengajuan</div>
                                <div class="w-1/4">Jumlah Diajukan</div>
                                <div class="w-1/4">Jumlah Disetujui</div>
                                <div class="w-1/4 text-center">Status</div>
                            </div>
                            
                            {{-- Konten Riwayat (diisi oleh JS) --}}
                            <div id="riwayat-list">
                                {{-- Data riwayat akan dimasukkan di sini oleh JavaScript --}}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchButton = document.getElementById('search-button');
            const nikInput = document.getElementById('nik_search');
            const searchFeedback = document.getElementById('search-feedback');
            const resultArea = document.getElementById('result-area');
            const anggotaNameEl = document.getElementById('anggota-name');
            const riwayatListDiv = document.getElementById('riwayat-list');

            searchButton.addEventListener('click', function () {
                const nik = nikInput.value.trim();
                searchFeedback.textContent = '';
                resultArea.classList.add('hidden');
                riwayatListDiv.innerHTML = ''; // Kosongkan riwayat sebelumnya

                if (!nik) {
                    searchFeedback.textContent = 'Silakan masukkan NIK.';
                    searchFeedback.className = 'mb-4 text-sm text-red-600';
                    return;
                }

                searchFeedback.textContent = 'Mencari...';
                searchFeedback.className = 'mb-4 text-sm text-gray-500';

                fetch(`{{ route('anggota.search.nik.riwayat') }}?nik=${nik}`, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        searchFeedback.textContent = '';
                        anggotaNameEl.textContent = data.anggota.nama; // Tampilkan nama anggota

                        // Tampilkan riwayat pinjaman
                        if (data.riwayat && data.riwayat.length > 0) {
                            data.riwayat.forEach(pinjaman => {
                                const row = document.createElement('div');
                                row.className = 'flex items-center py-3 border-b';

                                // Tgl Pengajuan
                                const tglCell = document.createElement('div');
                                tglCell.className = 'w-1/4 text-sm text-gray-600';
                                tglCell.textContent = new Date(pinjaman.tanggal_pengajuan).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                                row.appendChild(tglCell);

                                // Jumlah Diajukan
                                const jmlAjuanCell = document.createElement('div');
                                jmlAjuanCell.className = 'w-1/4 text-sm text-gray-600';
                                jmlAjuanCell.textContent = `Rp ${parseInt(pinjaman.jumlah_pinjaman).toLocaleString('id-ID')}`;
                                row.appendChild(jmlAjuanCell);
                                
                                // Jumlah Disetujui
                                const jmlSetujuCell = document.createElement('div');
                                jmlSetujuCell.className = 'w-1/4 text-sm text-gray-600';
                                jmlSetujuCell.textContent = pinjaman.status === 'disetujui' ? `Rp ${parseInt(pinjaman.jumlah_disetujui).toLocaleString('id-ID')}` : '-';
                                row.appendChild(jmlSetujuCell);


                                // Status
                                const statusCell = document.createElement('div');
                                statusCell.className = 'w-1/4 text-center';
                                const statusBadge = document.createElement('span');
                                statusBadge.className = 'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full';
                                statusBadge.textContent = pinjaman.status.charAt(0).toUpperCase() + pinjaman.status.slice(1);
                                if (pinjaman.status === 'disetujui') {
                                    statusBadge.classList.add('bg-green-100', 'text-green-800');
                                } else if (pinjaman.status === 'ditolak') {
                                    statusBadge.classList.add('bg-red-100', 'text-red-800');
                                } else {
                                    statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                                }
                                statusCell.appendChild(statusBadge);
                                row.appendChild(statusCell);

                                riwayatListDiv.appendChild(row);
                            });
                        } else {
                            riwayatListDiv.innerHTML = '<div class="text-center py-4 text-gray-500">Nasabah ini belum memiliki riwayat pinjaman.</div>';
                        }
                        
                        resultArea.classList.remove('hidden'); // Tampilkan area hasil
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