<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">Pengajuan Pinjaman Baru</h2>
                <p class="mt-2 text-gray-600">Khusus untuk nasabah yang sudah terdaftar (Repeat Order)</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <form id="searchForm" class="relative">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Cari NIK Nasabah</label>
                    <div class="flex gap-4">
                        <input type="text" id="nikInput" class="w-full px-5 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition text-lg" placeholder="Masukkan 16 digit NIK" maxlength="16">
                        <button type="submit" class="px-8 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
                            Cari
                        </button>
                    </div>
                    <p id="errorMsg" class="mt-2 text-red-500 text-sm hidden"></p>
                </form>
            </div>

            <div id="resultCard" class="hidden mt-8 bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-cyan-100">
                <div class="bg-cyan-50 px-8 py-4 border-b border-cyan-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-cyan-800">Nasabah Ditemukan</h3>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Terdaftar</span>
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-cyan-100 rounded-full flex items-center justify-center text-3xl">👤</div>
                        <div class="flex-1">
                            <h4 id="resNama" class="text-2xl font-bold text-gray-800"></h4>
                            <p id="resNik" class="text-gray-500 font-mono text-lg"></p>
                            <p id="resAlamat" class="text-gray-600 text-sm mt-1"></p>
                        </div>
                        <div>
                            <a id="btnLanjut" href="#" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-md flex items-center gap-2 transition-all transform hover:scale-105">
                                <span>Ajukan Pinjaman</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const nik = document.getElementById('nikInput').value;
            const errorMsg = document.getElementById('errorMsg');
            const resultCard = document.getElementById('resultCard');

            if(nik.length < 16) {
                errorMsg.innerText = "NIK harus 16 digit.";
                errorMsg.classList.remove('hidden');
                return;
            }

            // Panggil API Search (Gunakan API yang sudah ada di AnggotaController)
            // Pastikan route 'karyawan.anggota.cari' mengarah ke cariNasabahByNik di AnggotaController
            // Atau buat endpoint baru jika perlu. Kita pakai endpoint searchByNik yang sudah ada.
            
            fetch("{{ route('api.nasabah.cari') }}?nik=" + nik, { // Pastikan route ini ada di api.php atau web.php
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    errorMsg.classList.add('hidden');
                    document.getElementById('resNama').innerText = data.anggota.nama;
                    document.getElementById('resNik').innerText = data.anggota.nik || data.anggota.no_ktp; // Handle beda nama kolom
                    
                    // Update Link Tombol
                    const url = "{{ route('pinjaman.create.existing', ':id') }}";
                    document.getElementById('btnLanjut').href = url.replace(':id', data.anggota.id);
                    
                    resultCard.classList.remove('hidden');
                } else {
                    resultCard.classList.add('hidden');
                    errorMsg.innerText = data.message || "Nasabah tidak ditemukan.";
                    errorMsg.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error(err);
                errorMsg.innerText = "Terjadi kesalahan sistem.";
                errorMsg.classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>