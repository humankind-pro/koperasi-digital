<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Input Gaji & Potongan Otomatis</h3>

                <form method="POST" action="{{ route('sekertaris.gaji.store') }}">
                    @csrf
                    
                    {{-- 1. DATA UTAMA --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Pegawai</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="hitungGaji()">
                                <option value="">-- Pilih --</option>
                                @foreach ($pegawai as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Periode Gaji</label>
                            <input type="date" name="tanggal_gaji" id="tanggal_gaji" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ date('Y-m-d') }}" onchange="hitungGaji()">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Gaji Pokok (Rp)</label>
                            <input type="number" name="gaji_pokok" id="gaji_pokok" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-lg font-bold" placeholder="0" oninput="hitungGaji()">
                            <p class="text-xs text-gray-500 mt-1">*Gaji Pokok akan dibagi 26 untuk mendapatkan tarif harian.</p>
                        </div>
                    </div>

                    {{-- 2. KALKULASI OTOMATIS (READONLY) --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Detail Potongan (Otomatis dari Absensi)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            
                            <div>
                                <label class="text-xs text-gray-500">Masuk (Hari)</label>
                                {{-- PENTING: name="jumlah_hadir" sesuai database --}}
                                <input type="text" name="jumlah_hadir" id="jumlah_hadir" class="w-full bg-white border-none font-bold" readonly value="0">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Alpa (Hari)</label>
                                {{-- PENTING: name="jumlah_alpa" sesuai database --}}
                                <input type="text" name="jumlah_alpa" id="jumlah_alpa" class="w-full bg-white border-none font-bold text-red-600" readonly value="0">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Terlambat (Kali)</label>
                                {{-- PENTING: name="jumlah_terlambat" sesuai database --}}
                                <input type="text" name="jumlah_terlambat" id="jumlah_terlambat" class="w-full bg-white border-none font-bold text-orange-600" readonly value="0">
                            </div>
                            
                            <div class="md:col-span-1">
                                <label class="text-xs font-bold text-gray-700">Potongan Alpa (Rp)</label>
                                {{-- PENTING: name="nominal_potongan_alpa" sesuai database --}}
                                <input type="number" name="nominal_potongan_alpa" id="potongan_alpa" class="w-full bg-gray-200 border-gray-300 rounded text-red-700 font-bold" readonly value="0">
                            </div>
                            <div class="md:col-span-1">
                                <label class="text-xs font-bold text-gray-700">Potongan Telat (Rp)</label>
                                {{-- PENTING: name="nominal_potongan_terlambat" sesuai database --}}
                                <input type="number" name="nominal_potongan_terlambat" id="potongan_terlambat" class="w-full bg-gray-200 border-gray-300 rounded text-red-700 font-bold" readonly value="0">
                            </div>
                        </div>
                    </div>

                    {{-- 3. TAMBAHAN LAIN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tunjangan (Rp)</label>
                            <input type="number" name="tunjangan" id="tunjangan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="0" oninput="updateTotal()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Potongan Lain-lain (Rp)</label>
                            {{-- PENTING: name="potongan_lain" untuk dipetakan ke kolom 'potongan' di Controller --}}
                            <input type="number" name="potongan_lain" id="potongan_lain" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="0" oninput="updateTotal()">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="catatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2"></textarea>
                        </div>
                    </div>

                    {{-- TOTAL --}}
                    <div class="flex justify-between items-center border-t pt-4">
                        <div class="text-lg font-medium text-gray-600">Total Gaji Bersih:</div>
                        <div class="text-3xl font-bold text-indigo-700" id="display_total">Rp 0</div>
                    </div>

                    <div class="mt-6 text-right">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700">Simpan Data Gaji</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function hitungGaji() {
            const userId = document.getElementById('user_id').value;
            const tanggal = document.getElementById('tanggal_gaji').value;
            const gajiPokok = document.getElementById('gaji_pokok').value;

            if (!userId || !tanggal || !gajiPokok) return;

            const date = new Date(tanggal);
            
            // Panggil API
            fetch(`{{ route('sekertaris.gaji.hitung') }}?user_id=${userId}&bulan=${date.getMonth()+1}&tahun=${date.getFullYear()}&gaji_pokok=${gajiPokok}`)
                .then(res => res.json())
                .then(data => {
                    // Isi Field dengan ID yang sesuai
                    document.getElementById('jumlah_hadir').value = data.jumlah_hadir;
                    document.getElementById('jumlah_alpa').value = data.jumlah_alpa;
                    document.getElementById('jumlah_terlambat').value = data.jumlah_terlambat;
                    
                    // Isi Nominal Potongan
                    document.getElementById('potongan_alpa').value = data.nominal_potongan_alpa;
                    document.getElementById('potongan_terlambat').value = data.nominal_potongan_terlambat;
                    
                    updateTotal();
                })
                .catch(err => console.error(err));
        }

        function updateTotal() {
            const gapok = parseFloat(document.getElementById('gaji_pokok').value) || 0;
            const tunjangan = parseFloat(document.getElementById('tunjangan').value) || 0;
            
            const potAlpa = parseFloat(document.getElementById('potongan_alpa').value) || 0;
            const potTelat = parseFloat(document.getElementById('potongan_terlambat').value) || 0;
            const potLain = parseFloat(document.getElementById('potongan_lain').value) || 0;

            const total = (gapok + tunjangan) - (potAlpa + potTelat + potLain);
            document.getElementById('display_total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
</x-app-layout>