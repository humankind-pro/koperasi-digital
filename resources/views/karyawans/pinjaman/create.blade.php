<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Formulir Pengajuan Pinjaman</h3>

                    {{-- Area untuk pesan error/sukses dari pencarian NIK --}}
                    <div id="search-feedback" class="mb-4 text-sm"></div>

                    {{-- Form Pencarian NIK --}}
                    <div class="mb-6 flex items-end space-x-2">
                        <div class="flex-grow">
                            <label for="nik_search" class="block text-sm font-medium text-gray-700">Cari NIK Nasabah</label>
                            <input type="text" id="nik_search" name="nik_search" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" placeholder="Masukkan NIK">
                        </div>
                        <button type="button" id="search-button" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">
                            Cari
                        </button>
                    </div>

                    {{-- Area untuk menampilkan hasil pencarian --}}
                    <div id="search-result" class="mb-6 hidden p-4 bg-green-50 border border-green-200 rounded-md">
                        <p class="text-sm font-medium text-gray-700">Nasabah ditemukan:</p>
                        <p id="anggota-name" class="text-lg font-semibold text-gray-900"></p>
                    </div>

                    {{-- Form Pengajuan Pinjaman (Awalnya disembunyikan) --}}
                    <form method="POST" action="{{ route('pinjaman.store') }}" id="loan-form" class="hidden">
                        @csrf
                        {{-- Input tersembunyi untuk menyimpan ID anggota --}}
                        <input type="hidden" name="anggota_id" id="anggota_id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="jumlah_pinjaman" value="Jumlah Pinjaman (Rp)" />
                                <x-text-input id="jumlah_pinjaman" class="block mt-1 w-full" type="number" name="jumlah_pinjaman" :value="old('jumlah_pinjaman')" required />
                            </div>
                            <div>
                                <x-input-label for="tenor_bulan" value="Tenor (Bulan)" />
                                <x-text-input id="tenor_bulan" class="block mt-1 w-full" type="number" name="tenor_bulan" :value="old('tenor_bulan')" required />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="tujuan" value="Tujuan Pinjaman" />
                                <textarea id="tujuan" name="tujuan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('tujuan') }}</textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="px-4 py-2 bg-cyan-500 border rounded-md font-semibold text-xs text-white uppercase hover:bg-cyan-600">
                                Kirim Pengajuan Pinjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tambahkan script di bagian bawah --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchButton = document.getElementById('search-button');
            const nikInput = document.getElementById('nik_search');
            const searchFeedback = document.getElementById('search-feedback');
            const searchResultDiv = document.getElementById('search-result');
            const anggotaNameEl = document.getElementById('anggota-name');
            const anggotaIdInput = document.getElementById('anggota_id');
            const loanForm = document.getElementById('loan-form');

            searchButton.addEventListener('click', function () {
                const nik = nikInput.value.trim();
                searchFeedback.textContent = ''; // Clear previous feedback
                searchResultDiv.classList.add('hidden'); // Hide result div
                loanForm.classList.add('hidden'); // Hide form

                if (!nik) {
                    searchFeedback.textContent = 'Silakan masukkan NIK.';
                    searchFeedback.className = 'mb-4 text-sm text-red-600';
                    return;
                }

                // Tampilkan loading (opsional)
                searchFeedback.textContent = 'Mencari...';
                searchFeedback.className = 'mb-4 text-sm text-gray-500';

                // Kirim request AJAX ke server
                fetch(`{{ route('anggota.search.nik') }}?nik=${nik}`, { // Ganti jika nama route berbeda
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Penting untuk request AJAX
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.anggota) {
                        searchFeedback.textContent = '';
                        anggotaNameEl.textContent = data.anggota.nama;
                        anggotaIdInput.value = data.anggota.id;
                        searchResultDiv.classList.remove('hidden');
                        loanForm.classList.remove('hidden');
                    } else {
                        searchFeedback.textContent = data.message || 'Nasabah tidak ditemukan atau belum disetujui.';
                        searchFeedback.className = 'mb-4 text-sm text-red-600';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchFeedback.textContent = 'Terjadi kesalahan saat mencari. Coba lagi.';
                    searchFeedback.className = 'mb-4 text-sm text-red-600';
                });
            });
        });
    </script>
    @endpush

</x-app-layout>