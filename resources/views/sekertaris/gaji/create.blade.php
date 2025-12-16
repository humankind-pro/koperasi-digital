<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-6 border-b pb-2">Hitung Gaji (Sistem Harian)</h3>

            <form method="POST" action="{{ route('sekertaris.gaji.store') }}">
                @csrf
                
                {{-- INPUT UTAMA --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Pilih Pegawai</label>
                        <select name="user_id" id="user_id" class="w-full border rounded p-2" onchange="hitungGaji()">
                            <option value="">-- Pilih --</option>
                            @foreach ($pegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Periode Gaji</label>
                        <input type="date" name="tanggal_gaji" id="tanggal_gaji" class="w-full border rounded p-2" value="{{ date('Y-m-d') }}" onchange="hitungGaji()">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-blue-700">Gaji Per Hari (Rate Harian)</label>
                        <input type="number" name="gaji_pokok" id="gaji_pokok" class="w-full border border-blue-300 rounded p-2 font-bold text-lg" placeholder="Contoh: 100000" oninput="hitungGaji()">
                        <p class="text-xs text-gray-500 mt-1">*Masukkan nominal bayaran untuk 1 hari kerja.</p>
                    </div>
                </div>

                {{-- HASIL HITUNG OTOMATIS --}}
                <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-6">
                    <h4 class="font-bold text-gray-700 mb-3">Rincian Kehadiran</h4>
                    
                    <div class="grid grid-cols-3 gap-4 text-center mb-4">
                        <div class="bg-white p-2 rounded shadow-sm">
                            <span class="block text-xs text-gray-500">Total Masuk</span>
                            <span id="txt_hadir" class="font-bold text-lg text-green-600">0 Hari</span>
                            <input type="hidden" name="jumlah_hadir" id="jumlah_hadir">
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm">
                            <span class="block text-xs text-gray-500">Terlambat (>09:00)</span>
                            <span id="txt_telat" class="font-bold text-lg text-orange-500">0 Kali</span>
                            <input type="hidden" name="jumlah_terlambat" id="jumlah_terlambat">
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm">
                            <span class="block text-xs text-gray-500">Hari Minggu/Bolos</span>
                            <span class="font-bold text-lg text-gray-400">Rp 0</span>
                        </div>
                    </div>

                    {{-- TABEL RINCIAN HARIAN --}}
                    <div class="mb-4">
                        <p class="text-xs font-bold text-gray-600 mb-1">Log Pendapatan Harian:</p>
                        <div class="max-h-40 overflow-y-auto border rounded bg-white p-2 text-sm">
                            <ul id="list_rincian" class="space-y-1">
                                <li class="text-gray-400 italic">Silakan pilih pegawai...</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- TAMBAHAN --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="text-sm font-bold">Tunjangan (+)</label>
                        <input type="number" name="tunjangan" id="tunjangan" class="w-full border rounded" value="0" oninput="updateTotal()">
                    </div>
                    <div>
                        <label class="text-sm font-bold">Potongan Lain (-)</label>
                        <input type="number" name="potongan_lain" id="potongan_lain" class="w-full border rounded" value="0" oninput="updateTotal()">
                    </div>
                </div>

                {{-- TOTAL AKHIR --}}
                <div class="flex justify-between items-center border-t pt-4 bg-blue-50 p-4 rounded">
                    <div>
                        <span class="text-gray-600 text-sm">Total Gaji Diterima:</span>
                        <div id="display_total" class="text-3xl font-bold text-blue-800">Rp 0</div>
                    </div>
                    {{-- Input hidden untuk menyimpan nilai akhir --}}
                    <input type="hidden" name="total_gaji" id="input_total_gaji" value="0">
                    
                    {{-- Input hidden dummy untuk field yang dihapus agar tidak error di controller store --}}
                    <input type="hidden" name="jumlah_alpa" value="0">
                    <input type="hidden" name="nominal_potongan_terlambat" value="0">
                    <input type="hidden" name="nominal_potongan_alpa" value="0">
                </div>

                <button type="submit" class="mt-6 w-full bg-blue-600 text-white py-3 rounded font-bold hover:bg-blue-700 shadow-lg">SIMPAN & CETAK SLIP</button>
            </form>
        </div>
    </div>

    <script>
        let gajiBersihDariSystem = 0; // Menyimpan nilai dari API

        function hitungGaji() {
            let user = document.getElementById('user_id').value;
            let tgl = document.getElementById('tanggal_gaji').value;
            let rate = document.getElementById('gaji_pokok').value;

            if(!user || !tgl || !rate) return;

            let d = new Date(tgl);
            // Panggil API
            let url = `{{ route('sekertaris.gaji.hitung') }}?user_id=${user}&bulan=${d.getMonth()+1}&tahun=${d.getFullYear()}&gaji_pokok=${rate}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    // Update UI Atas
                    document.getElementById('txt_hadir').innerText = data.jumlah_hadir + " Hari";
                    document.getElementById('txt_telat').innerText = data.jumlah_terlambat + " Kali";
                    
                    // Update Input Hidden
                    document.getElementById('jumlah_hadir').value = data.jumlah_hadir;
                    document.getElementById('jumlah_terlambat').value = data.jumlah_terlambat;

                    // Simpan Gaji Bersih dari sistem ke variabel global
                    gajiBersihDariSystem = parseFloat(data.total_gaji_bersih);

                    // Render List Rincian
                    let list = document.getElementById('list_rincian');
                    list.innerHTML = '';
                    if(data.rincian.length > 0) {
                        data.rincian.forEach(row => {
                            // Warna teks hijau jika full, oranye jika telat
                            let colorClass = row.status.includes('100%') ? 'text-green-600' : 'text-orange-600 font-bold';
                            list.innerHTML += `
                                <li class="flex justify-between border-b border-gray-100 pb-1">
                                    <span>${row.tanggal} <span class="text-xs text-gray-500">(${row.status})</span></span>
                                    <span class="${colorClass}">Rp ${row.nominal}</span>
                                </li>
                            `;
                        });
                    } else {
                        list.innerHTML = '<li class="text-red-500">Tidak ada data kehadiran bulan ini.</li>';
                    }

                    updateTotal();
                });
        }

        function updateTotal() {
            let tunjangan = parseFloat(document.getElementById('tunjangan').value) || 0;
            let potLain = parseFloat(document.getElementById('potongan_lain').value) || 0;

            // Rumus: (Gaji dari Sistem Absensi) + Tunjangan - Potongan Lain
            let finalTotal = gajiBersihDariSystem + tunjangan - potLain;

            // Pastikan tidak minus
            if(finalTotal < 0) finalTotal = 0;

            document.getElementById('display_total').innerText = 'Rp ' + finalTotal.toLocaleString('id-ID');
            document.getElementById('input_total_gaji').value = finalTotal;
        }
    </script>
</x-app-layout>