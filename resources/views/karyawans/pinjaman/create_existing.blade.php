<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('pinjaman.store') }}" method="POST">
                @csrf
                <input type="hidden" name="anggota_id" value="{{ $anggota->id }}">

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6 border-l-8 border-cyan-600">
                    <div class="p-6 flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Data Peminjam</h3>
                            <div class="mt-2 grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                                <p class="text-gray-500">Nama:</p>
                                <p class="font-bold text-gray-800">{{ $anggota->nama }}</p>
                                
                                <p class="text-gray-500">NIK:</p>
                                <p class="font-bold text-gray-800">{{ $anggota->nik }}</p>
                                
                                <p class="text-gray-500">Pekerjaan:</p>
                                <p class="font-bold text-gray-800">{{ $anggota->pekerjaan ?? '-' }}</p>

                                <p class="text-gray-500">Skor Kredit:</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $anggota->skor_kredit }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-lg text-xs font-bold uppercase tracking-wide">
                                Nasabah Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Rincian Pengajuan Baru
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Pengajuan (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500 font-bold">Rp</span>
                                <input type="number" name="jumlah_pinjaman" class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 font-bold text-lg" placeholder="0" required min="100000">
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tenor (Bulan)</label>
                            <select name="tenor_bulan" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" required>
                                <option value="">Pilih Tenor</option>
                                @foreach([1,2,3,4,5,6,7,8,9,10,11,12,18,24,36,48,60] as $t)
                                    <option value="{{ $t }}">{{ $t }} Bulan</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tujuan Penggunaan</label>
                            <select name="tujuan" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" required>
                                <option value="Modal Usaha">Modal Usaha</option>
                                <option value="Pendidikan">Pendidikan</option>
                                <option value="Renovasi">Renovasi</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Konsumtif">Konsumtif</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Jaminan</label>
                            <select name="jenis_jaminan" id="jenis_jaminan" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" required>
                                <option value="BPKB Motor">BPKB Motor</option>
                                <option value="BPKB Mobil">BPKB Mobil</option>
                                <option value="Sertifikat Tanah">Sertifikat Tanah</option>
                                <option value="SK Karyawan">SK Karyawan</option>
                                <option value="Tanpa Jaminan">Tanpa Jaminan (Khusus Limit Kecil)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 hidden" id="ket_jaminan_container">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan Jaminan Lainnya</label>
                            <input type="text" name="keterangan_jaminan" id="keterangan_jaminan" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Sebutkan detail jaminan...">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 border-t pt-6">
                        <a href="{{ route('pinjaman.search') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-xl transition">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                            Ajukan Pinjaman
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        // Toggle input keterangan jaminan
        document.getElementById('jenis_jaminan').addEventListener('change', function() {
            const container = document.getElementById('ket_jaminan_container');
            const input = document.getElementById('keterangan_jaminan');
            
            if(this.value === 'Lainnya') {
                container.classList.remove('hidden');
                input.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                input.removeAttribute('required');
                input.value = '';
            }
        });
    </script>
</x-app-layout>