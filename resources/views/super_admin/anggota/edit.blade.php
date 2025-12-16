<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                
                {{-- Header Section --}}
                <div class="px-8 py-6 bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white">
                    <h3 class="text-2xl font-bold">Edit Data Nasabah</h3>
                    <p class="text-cyan-100 text-sm mt-1">Perbarui informasi nasabah secara lengkap.</p>
                </div>

                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.anggota.update', $anggota->id) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div>
                            <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Identitas Pribadi
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {{-- NIK --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">NIK</label>
                                    <input type="text" name="nik" value="{{ old('nik', $anggota->nik ?? $anggota->no_ktp) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition" required>
                                </div>

                                {{-- Nama --}}
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" name="nama" value="{{ old('nama', $anggota->nama) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition" required>
                                </div>

                                {{-- Tempat Lahir --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $anggota->tempat_lahir) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $anggota->tanggal_lahir) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                        <option value="L" @selected(old('jenis_kelamin', $anggota->jenis_kelamin) == 'L')>Laki-laki</option>
                                        <option value="P" @selected(old('jenis_kelamin', $anggota->jenis_kelamin) == 'P')>Perempuan</option>
                                    </select>
                                </div>

                                {{-- Agama --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Agama</label>
                                    <select name="agama" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                        @foreach(['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU'] as $agm)
                                            <option value="{{ $agm }}" @selected(old('agama', $anggota->agama) == $agm)>{{ ucfirst(strtolower($agm)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Perkawinan --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Perkawinan</label>
                                    <select name="status_perkawinan" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                        @foreach(['BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI'] as $sts)
                                            <option value="{{ $sts }}" @selected(old('status_perkawinan', $anggota->status_perkawinan) == $sts)>{{ ucfirst(strtolower($sts)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Gol Darah --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Golongan Darah</label>
                                    <select name="gol_darah" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                        <option value="-">-</option>
                                        @foreach(['A', 'B', 'AB', 'O'] as $gol)
                                            <option value="{{ $gol }}" @selected(old('gol_darah', $anggota->gol_darah) == $gol)>{{ $gol }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat & Kontak
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="lg:col-span-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap (Jalan)</label>
                                    <textarea name="alamat" rows="2" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">{{ old('alamat', $anggota->alamat) }}</textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">RT</label>
                                    <input type="text" name="rt" value="{{ old('rt', $anggota->rt) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">RW</label>
                                    <input type="text" name="rw" value="{{ old('rw', $anggota->rw) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kelurahan/Desa</label>
                                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $anggota->kelurahan) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kecamatan</label>
                                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $anggota->kecamatan) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kabupaten/Kota</label>
                                    <input type="text" name="kabupaten_kota" value="{{ old('kabupaten_kota', $anggota->kabupaten_kota) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Provinsi</label>
                                    <input type="text" name="provinsi" value="{{ old('provinsi', $anggota->provinsi) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor WhatsApp</label>
                                    <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $anggota->nomor_telepon) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition" required>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pekerjaan & Finansial
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan</label>
                                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $anggota->pekerjaan) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Lama Bekerja (Tahun)</label>
                                    <input type="number" step="0.1" name="lama_bekerja" value="{{ old('lama_bekerja', $anggota->lama_bekerja) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Tempat Tinggal</label>
                                    <select name="status_tempat_tinggal" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                        @foreach(['Milik Sendiri', 'Milik Orang Tua', 'Sewa', 'Kontrak'] as $stt)
                                            <option value="{{ $stt }}" @selected(old('status_tempat_tinggal', $anggota->status_tempat_tinggal) == $stt)>{{ $stt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Tanggungan</label>
                                    <input type="number" name="jumlah_tanggungan" value="{{ old('jumlah_tanggungan', $anggota->jumlah_tanggungan) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pendapatan Bulanan (Rp)</label>
                                    <input type="number" name="pendapatan_bulanan" value="{{ old('pendapatan_bulanan', $anggota->pendapatan_bulanan) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pengeluaran Bulanan (Rp)</label>
                                    <input type="number" name="pengeluaran_bulanan" value="{{ old('pengeluaran_bulanan', $anggota->pengeluaran_bulanan) }}" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition">
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-100 p-6 rounded-2xl border border-gray-200">
                            <h4 class="text-lg font-bold text-gray-800 border-b border-gray-300 pb-2 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Status & Validasi
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Pengajuan</label>
                                    <select name="status" class="w-full rounded-xl border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 transition font-bold" required>
                                        <option value="pending" class="text-yellow-600" @selected(old('status', $anggota->status) == 'pending')>⏳ Pending</option>
                                        <option value="disetujui" class="text-green-600" @selected(old('status', $anggota->status) == 'disetujui')>✅ Disetujui</option>
                                        <option value="ditolak" class="text-red-600" @selected(old('status', $anggota->status) == 'ditolak')>❌ Ditolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('superadmin.anggota.index') }}" class="px-6 py-3 rounded-xl bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold shadow-lg hover:shadow-xl hover:from-cyan-700 hover:to-blue-700 transition transform hover:-translate-y-0.5">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>