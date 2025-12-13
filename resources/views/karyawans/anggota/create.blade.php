<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Gagal Menyimpan!</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Form Pendaftaran Anggota Lengkap</h2>

                    <form action="{{ route('anggota.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="col-span-2 bg-blue-50 p-4 rounded border border-blue-200">
                                <h3 class="font-bold text-lg mb-3 text-blue-800">Data Pribadi</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="nama" value="Nama Lengkap" />
                                        <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required />
                                    </div>
                                    <div>
                                        <x-input-label for="no_ktp" value="NIK (KTP)" />
                                        <x-text-input id="no_ktp" class="block mt-1 w-full" type="number" name="no_ktp" :value="old('no_ktp')" required />
                                    </div>
                                    <div>
                                        <x-input-label for="umur" value="Umur (Tahun)" />
                                        <x-text-input id="umur" class="block mt-1 w-full" type="number" name="umur" :value="old('umur')" required />
                                    </div>
                                    <div>
                                        <x-input-label for="pendidikan" value="Pendidikan Terakhir" />
                                        <select name="pendidikan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="SD">SD</option>
                                            <option value="SMP">SMP</option>
                                            <option value="SMA">SMA/SMK</option>
                                            <option value="D3">Diploma (D3)</option>
                                            <option value="S1">Sarjana (S1)</option>
                                            <option value="S2">Magister (S2)</option>
                                            <option value="S3">Doktor (S3)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="tanggungan" value="Jumlah Tanggungan" />
                                        <x-text-input id="tanggungan" class="block mt-1 w-full" type="number" name="tanggungan" value="0" required />
                                    </div>
                                    <div>
                                        <x-input-label for="status_tempat_tinggal" value="Status Tempat Tinggal" />
                                        <select name="status_tempat_tinggal" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="Milik Sendiri">Milik Sendiri</option>
                                            <option value="Sewa">Sewa / Kos</option>
                                            <option value="Kontrak">Kontrak</option>
                                            <option value="Bersama Orang Tua">Bersama Orang Tua</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="nomor_telepon" value="Nomor HP / WA" />
                                        <x-text-input id="nomor_telepon" class="block mt-1 w-full" type="text" name="nomor_telepon" required />
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label for="alamat" value="Alamat Lengkap" />
                                        <textarea name="alamat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 bg-green-50 p-4 rounded border border-green-200">
                                <h3 class="font-bold text-lg mb-3 text-green-800">Pekerjaan & Keuangan</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="pekerjaan" value="Pekerjaan" />
                                        <x-text-input id="pekerjaan" class="block mt-1 w-full" type="text" name="pekerjaan" required />
                                    </div>
                                    <div>
                                        <x-input-label for="lama_bekerja_tahun" value="Lama Bekerja (Tahun)" />
                                        <x-text-input id="lama_bekerja_tahun" class="block mt-1 w-full" type="number" name="lama_bekerja_tahun" required />
                                    </div>
                                    <div>
                                        <x-input-label for="pendapatan_bulanan" value="Pendapatan Bulanan (Rp)" />
                                        <x-text-input id="pendapatan_bulanan" class="block mt-1 w-full" type="number" name="pendapatan_bulanan" required />
                                    </div>
                                    <div>
                                        <x-input-label for="pengeluaran_bulanan" value="Pengeluaran Bulanan (Rp)" />
                                        <x-text-input id="pengeluaran_bulanan" class="block mt-1 w-full" type="number" name="pengeluaran_bulanan" required />
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 bg-yellow-50 p-4 rounded border border-yellow-200">
                                <h3 class="font-bold text-lg mb-3 text-yellow-800">Pinjaman & Jaminan</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    
                                    <div>
                                        <x-input-label for="tujuan_pinjaman_preferensi" value="Tujuan Pinjaman Utama" />
                                        <select name="tujuan_pinjaman_preferensi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="MODAL USAHA">Modal Usaha</option>
                                            <option value="PENDIDIKAN">Pendidikan</option>
                                            <option value="RENOVASI RUMAH">Renovasi Rumah</option>
                                            <option value="KESEHATAN">Kesehatan</option>
                                            <option value="PERNIKAHAN">Pernikahan</option>
                                            <option value="KONSUMTIF">Konsumtif (Barang/Kendaraan)</option>
                                            <option value="INVENTARIS">Inventaris</option>
                                        </select>
                                    </div>

                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="check_jaminan" name="memiliki_jaminan" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" onclick="toggleJaminan()">
                                            <span class="ml-2 text-gray-800 font-bold">Apakah nasabah memiliki Jaminan?</span>
                                        </label>
                                    </div>

                                    <div id="box_jaminan" class="hidden p-4 bg-white border rounded">
                                        <x-input-label for="jenis_jaminan" value="Pilih Jenis Jaminan" />
                                        <select id="jenis_jaminan" name="jenis_jaminan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" onchange="cekJaminanLainnya()">
                                            <option value="">-- Pilih --</option>
                                            <option value="BPKB MOTOR">BPKB Motor</option>
                                            <option value="BPKB MOBIL">BPKB Mobil</option>
                                            <option value="SERTIFIKAT TANAH">Sertifikat Tanah</option>
                                            <option value="SERTIFIKAT RUMAH">Sertifikat Rumah</option>
                                            <option value="SLIP GAJI">SK / Slip Gaji</option>
                                            <option value="DEPOSITO">Deposito</option>
                                            <option value="EMAS">Emas</option>
                                            <option value="LAINNYA">Lainnya (Input Sendiri)</option>
                                        </select>

                                        <div id="box_lainnya" class="hidden mt-3">
                                            <x-input-label for="deskripsi_jaminan_lainnya" value="Tuliskan Jaminan Lainnya" />
                                            <x-text-input id="deskripsi_jaminan_lainnya" class="block mt-1 w-full" type="text" name="deskripsi_jaminan_lainnya" placeholder="Contoh: Kios Pasar" />
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4 bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Simpan Data Anggota') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleJaminan() {
            const checkBox = document.getElementById('check_jaminan');
            const box = document.getElementById('box_jaminan');
            if (checkBox.checked) {
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
                document.getElementById('jenis_jaminan').value = ""; // Reset
            }
        }

        function cekJaminanLainnya() {
            const select = document.getElementById('jenis_jaminan');
            const boxLainnya = document.getElementById('box_lainnya');
            const inputLainnya = document.getElementById('deskripsi_jaminan_lainnya');

            if (select.value === 'LAINNYA') {
                boxLainnya.classList.remove('hidden');
                inputLainnya.required = true;
            } else {
                boxLainnya.classList.add('hidden');
                inputLainnya.required = false;
                inputLainnya.value = "";
            }
        }
    </script>
</x-app-layout>