<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-white shadow-2xl rounded-3xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-white">Pengajuan Nasabah Baru</h3>
                            <p class="mt-2 text-cyan-100 text-lg">Upload KTP</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-6 py-4">
                                <p class="text-white text-sm font-medium">Terintegrasi Sistem</p>
                                <p class="text-white text-3xl font-bold text-center">OCR</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-xl shadow-sm">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <form id="formAnggota" method="POST" action="{{ route('anggota.store') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        <!-- SECTION 1: Upload Dokumen -->
                        <div class="space-y-5">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    1
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800">Upload Dokumen Identitas</h4>
                                    <p class="text-gray-600">Pastikan foto jelas dan tidak blur</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Foto KTP -->
                                <div class="group">
                                    <label class="flex items-center text-sm font-bold text-gray-700 mb-3">
                                        <svg class="w-5 h-5 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                        Foto KTP <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <label for="foto_ktp" class="block cursor-pointer">
                                        <div class="border-3 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-cyan-500 hover:bg-cyan-50/30 transition-all duration-300 group-hover:shadow-lg">
                                            <svg class="mx-auto h-14 w-14 text-gray-400 group-hover:text-cyan-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <p class="mt-3 text-base text-gray-600">
                                                <span class="font-semibold text-cyan-600">Klik untuk upload KTP</span>
                                            </p>
                                            <p class="mt-1 text-sm text-gray-500">PNG, JPG hingga 5MB</p>
                                        </div>
                                        <input id="foto_ktp" name="foto_ktp" type="file" accept="image/*" class="hidden" required/>
                                    </label>
                                    <div id="preview-ktp" class="mt-4 hidden">
                                        <img id="img-ktp" class="rounded-2xl shadow-xl max-h-64 mx-auto border-4 border-white" alt="Preview KTP"/>
                                    </div>
                                </div>
                                <!-- Foto Selfie -->
                                <div class="group">
                                    <label class="flex items-center text-sm font-bold text-gray-700 mb-3">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Selfie + KTP <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <label for="foto_selfie_ktp" class="block cursor-pointer">
                                        <div class="border-3 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-green-500 hover:bg-green-50/30 transition-all duration-300 group-hover:shadow-lg">
                                            <svg class="mx-auto h-14 w-14 text-gray-400 group-hover:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <circle cx="24" cy="18" r="8" stroke-width="2"/>
                                                <path d="M8 42v-6a8 8 0 018-8h16a8 8 0 018 8v6" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            <p class="mt-3 text-base text-gray-600">
                                                <span class="font-semibold text-green-600">Upload Selfie + KTP</span>
                                            </p>
                                            <p class="mt-1 text-sm text-gray-500">Pegang KTP di depan wajah</p>
                                        </div>
                                        <input id="foto_selfie_ktp" name="foto_selfie_ktp" type="file" accept="image/*" class="hidden" required/>
                                    </label>
                                    <div id="preview-selfie" class="mt-4 hidden">
                                        <img id="img-selfie" class="rounded-2xl shadow-xl max-h-64 mx-auto border-4 border-white" alt="Preview Selfie"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Loading OCR -->
                        <div id="ocr-loading" class="hidden text-center py-12">
                            <div class="inline-flex flex-col items-center space-y-4">
                                <svg class="animate-spin h-16 w-16 text-cyan-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800">Sedang membaca KTP...</p>
                                    <p class="text-gray-600 mt-2">(FYI) Ktp tidak selamanya terbaca</p>
                                </div>
                            </div>
                        </div>
                        <!-- SECTION 2: Data KTP (OCR Result) - WIDER TABLE LAYOUT -->
                        <div id="ocr-result" class="hidden space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    2
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800">Data Identitas dari KTP</h4>
                                    <p class="text-gray-600">Verifikasi dan lengkapi data jika diperlukan</p>
                                </div>
                            </div>
                            <!-- OCR Preview Card - WIDER GRID -->
                            <div class="bg-gradient-to-br from-cyan-50 via-blue-50 to-indigo-50 rounded-3xl p-6 border-2 border-cyan-200 shadow-xl">
                                <div class="flex items-center justify-between mb-5">
                                    <div class="flex items-center space-x-3">
                                        <div id="ocr-status-icon" class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <h5 id="ocr-status-title" class="text-xl font-bold text-cyan-800">Data KTP Terdeteksi</h5>
                                    </div>
                                    <button type="button" id="btn-edit-manual" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Manual
                                    </button>
                                </div>
                                <!-- WIDER GRID LAYOUT - 4 columns instead of 3 -->
                                <div id="ocr-preview-display" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">NIK</p>
                                        <p id="preview-nik" class="text-sm font-bold text-gray-800 font-mono"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm lg:col-span-2">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Nama Lengkap</p>
                                        <p id="preview-nama" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Tempat/Tgl Lahir</p>
                                        <p id="preview-ttl" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Jenis Kelamin</p>
                                        <p id="preview-jk" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm lg:col-span-3">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Alamat</p>
                                        <p id="preview-alamat" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">RT/RW</p>
                                        <p id="preview-rtrw" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Kelurahan/Desa</p>
                                        <p id="preview-keldesa" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Kecamatan</p>
                                        <p id="preview-kecamatan" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Kabupaten/Kota</p>
                                        <p id="preview-kabkota" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Provinsi</p>
                                        <p id="preview-provinsi" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Agama</p>
                                        <p id="preview-agama" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Status Perkawinan</p>
                                        <p id="preview-status" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Pekerjaan</p>
                                        <p id="preview-pekerjaan" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 shadow-sm">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Golongan Darah</p>
                                        <p id="preview-goldarah" class="text-sm font-bold text-gray-800"></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Manual Input Section - WIDER GRID -->
                            <div id="manual-input-section" class="hidden bg-white border-3 border-amber-400 rounded-3xl p-6 shadow-2xl">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </div>
                                        <h5 class="text-xl font-bold text-amber-800">Edit Data Manual</h5>
                                    </div>
                                    <button type="button" id="btn-cancel-edit" class="px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all shadow-lg">
                                        Batal Edit
                                    </button>
                                </div>
                                <!-- WIDER GRID - 4 columns -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            NIK <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nik" id="input-nik" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="16 digit" maxlength="16" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama" id="input-nama" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Sesuai KTP" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="input-tempat-lahir" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Kota">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Lahir</label>
                                        <input type="text" name="tanggal_lahir" id="input-tanggal-lahir" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="DD-MM-YYYY">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            Jenis Kelamin <span class="text-red-500">*</span>
                                        </label>
                                        <select name="jenis_kelamin" id="input-jenis-kelamin" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Golongan Darah</label>
                                        <select name="gol_darah" id="input-gol-darah" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                                            <option value="">-- Pilih --</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2 lg:col-span-4">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat</label>
                                        <textarea name="alamat" id="input-alamat" rows="2" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Alamat lengkap"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">RT</label>
                                        <input type="text" name="rt" id="input-rt" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="001" maxlength="3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">RW</label>
                                        <input type="text" name="rw" id="input-rw" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="001" maxlength="3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Kelurahan/Desa</label>
                                        <input type="text" name="kelurahan" id="input-kelurahan" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Kelurahan">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Kecamatan</label>
                                        <input type="text" name="kecamatan" id="input-kecamatan" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Kecamatan">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Kabupaten/Kota</label>
                                        <input type="text" name="kabupaten_kota" id="input-kabupaten-kota" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Kabupaten/Kota">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Provinsi</label>
                                        <input type="text" name="provinsi" id="input-provinsi" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Provinsi">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Agama</label>
                                        <select name="agama" id="input-agama" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                                            <option value="">-- Pilih --</option>
                                            <option value="ISLAM">Islam</option>
                                            <option value="KRISTEN">Kristen</option>
                                            <option value="KATOLIK">Katolik</option>
                                            <option value="HINDU">Hindu</option>
                                            <option value="BUDDHA">Buddha</option>
                                            <option value="KONGHUCU">Konghucu</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Status Perkawinan</label>
                                        <select name="status_perkawinan" id="input-status-perkawinan" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                                            <option value="">-- Pilih --</option>
                                            <option value="BELUM KAWIN">Belum Kawin</option>
                                            <option value="KAWIN">Kawin</option>
                                            <option value="CERAI HIDUP">Cerai Hidup</option>
                                            <option value="CERAI MATI">Cerai Mati</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Pekerjaan</label>
                                        <input type="text" name="pekerjaan" id="input-pekerjaan" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" placeholder="Pekerjaan">
                                    </div>
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <button type="button" id="btn-save-manual" class="px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 mb-6">
                            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-500 p-4 rounded-r-xl shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-yellow-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-lg font-bold text-yellow-800">Informasi Credit Score</h4>
                                        <p class="text-sm text-yellow-700">Credit Score untuk nasabah baru ini adalah <strong>50</strong>. Nilai ini dihitung secara otomatis oleh sistem untuk User baru.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Credit Score Information -->
                        <div id="additional-data-section" class="hidden space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    3
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800">Data Kontak & Sosial</h4>
                                    <p class="text-gray-600">Informasi untuk komunikasi dan verifikasi</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-3xl p-6 shadow-lg border-2 border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                            Nomor WhatsApp <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nomor_telepon" id="nomor_telepon" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" placeholder="081234567890" required>
                                    </div>
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222a12.083 12.083 0 01.665 6.479L12 20.055a11.952 11.952 0 006.824-2.998 12.078 12.078 0 01.665 6.479L12 14z"/>
                                            </svg>
                                            Pendidikan <span class="text-red-500">*</span>
                                        </label>
                                        <select name="pendidikan" id="pendidikan" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="SD">1. SD</option>
                                            <option value="SMP">2. SMP</option>
                                            <option value="SMA/SMK">3. SMA?SMK</option>
                                            <option value="D3">4. Diploma</option> <!-- Assuming D3 maps to Diploma -->
                                            <option value="S1">5. S1</option>
                                            <option value="S2">6. S2</option>
                                            <option value="S3">7. S3</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Tanggungan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="jumlah_tanggungan" id="jumlah_tanggungan" min="0" max="20" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" placeholder="0" required>
                                    </div>
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Umur (Tahun) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="umur" id="umur" min="17" max="100" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" placeholder="35" required>
                                    </div>
                                    <!-- Lama Bekerja (Hanya Numerik) -->
                                    <div class="lg:col-span-2">
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <div class="p-1.5 bg-blue-100 rounded-xl mr-2">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            Lama Bekerja (Tahun) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <span class="text-blue-600 font-bold text-lg">⏰</span>
                                            </div>
                                            <input type="number" name="Lama_Bekerja_Tahun" id="lama_bekerja" min="0" max="50" step="0.1" class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-3 focus:ring-blue-400 focus:border-blue-500 transition-all duration-300 text-lg font-medium placeholder-blue-300" placeholder="Contoh: 5.5" required>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <div class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-medium">
                                                    Angka Tahun
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-2">
                                            <div class="h-1 flex-1 bg-blue-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 transition-all duration-500" style="width: 0%;" id="kerja-progress"></div>
                                            </div>
                                            <span class="text-xs font-medium text-blue-600" id="kerja-value">0 tahun</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Pengalaman kerja minimal 6 bulan (0.5 tahun) untuk pengajuan pinjaman</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Status Tempat Tinggal -->
                            <div class="bg-gradient-to-br from-violet-50 to-fuchsia-50 rounded-3xl p-6 border-2 border-violet-200">
                                <h5 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m9 4h1a3 3 0 01-3-3V7a3 3 0 013-3z"/>
                                    </svg>
                                    Status Tempat Tinggal <span class="text-red-500 ml-1">*</span>
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                                    <!-- Milik Sendiri -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="status_tempat_tinggal" value="Milik Sendiri" class="peer sr-only" required>
                                        <div class="peer-checked:bg-violet-500 peer-checked:text-white bg-white border-2 border-violet-200 rounded-xl p-4 text-center transition-all hover:border-violet-300 peer-checked:border-violet-500 peer-checked:shadow-lg">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-violet-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            <p class="text-sm font-bold">Milik Sendiri</p>
                                            <p class="text-xs mt-1 peer-checked:text-white text-gray-600">Pemilik rumah</p>
                                        </div>
                                    </label>
                                    <!-- Sewa -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="status_tempat_tinggal" value="Sewa" class="peer sr-only">
                                        <div class="peer-checked:bg-violet-500 peer-checked:text-white bg-white border-2 border-violet-200 rounded-xl p-4 text-center transition-all hover:border-violet-300 peer-checked:border-violet-500 peer-checked:shadow-lg">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-violet-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                            </svg>
                                            <p class="text-sm font-bold">Sewa</p>
                                            <p class="text-xs mt-1 peer-checked:text-white text-gray-600">Kontrak tahunan</p>
                                        </div>
                                    </label>
                                    <!-- Kontrak -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="status_tempat_tinggal" value="Kontrak" class="peer sr-only">
                                        <div class="peer-checked:bg-violet-500 peer-checked:text-white bg-white border-2 border-violet-200 rounded-xl p-4 text-center transition-all hover:border-violet-300 peer-checked:border-violet-500 peer-checked:shadow-lg">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-violet-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-sm font-bold">Kontrak</p>
                                            <p class="text-xs mt-1 peer-checked:text-white text-gray-600">Jangka pendek</p>
                                        </div>
                                    </label>
                                    <!-- Rumah Orang Tua -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="status_tempat_tinggal" value="Rumah Orang Tua" class="peer sr-only">
                                        <div class="peer-checked:bg-violet-500 peer-checked:text-white bg-white border-2 border-violet-200 rounded-xl p-4 text-center transition-all hover:border-violet-300 peer-checked:border-violet-500 peer-checked:shadow-lg">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-violet-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="text-sm font-bold">Bersama Orang Tua</p>
                                            <p class="text-xs mt-1 peer-checked:text-white text-gray-600">Masih tinggal</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- SECTION 4 & 5: Financial & Loan Data - WIDER GRID -->
                        <div id="financial-data-section" class="hidden space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    4
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800">Informasi Finansial</h4>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl p-6 shadow-lg border-2 border-green-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Pendapatan Bulanan (Rp) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-3 text-gray-500 font-bold text-sm">Rp</span>
                                            <input type="number" name="pendapatan_bulanan" id="pendapatan_bulanan" class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" placeholder="5000000" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Pengeluaran Bulanan (Rp) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-3 text-gray-500 font-bold text-sm">Rp</span>
                                            <input type="number" name="pengeluaran_bulanan" id="pengeluaran_bulanan" class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" placeholder="3000000" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="loan-data-section" class="hidden space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-rose-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    5
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800">Rincian Pengajuan Pinjaman</h4>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-3xl p-6 shadow-lg border-2 border-rose-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Jumlah Pinjaman (Rp) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-3 text-gray-500 font-bold text-sm">Rp</span>
                                            <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition" placeholder="10000000" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm-3-7h10M7 10v5"/>
                                            </svg>
                                            Tenor Pinjaman <span class="text-red-500">*</span>
                                        </label>
                                        <select name="Lama_Tenor_Bulan" id="tenor" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition" required>
                                            <option value="">-- Pilih Tenor --</option>
                                            <option value="1">1 Bulan</option>
                                            <option value="2">2 Bulan</option>
                                            <option value="3">3 Bulan</option>
                                            <option value="4">4 Bulan</option>
                                            <option value="5">5 Bulan</option>
                                            <option value="6">6 Bulan</option>
                                            <option value="7">7 Bulan</option>
                                            <option value="8">8 Bulan</option>
                                            <option value="9">9 Bulan</option>
                                            <option value="10">10 Bulan</option>
                                            <option value="11">11 Bulan</option>
                                            <option value="12">12 Bulan (1 Tahun)</option>
                                            <option value="18">18 Bulan</option>
                                            <option value="24">24 Bulan (2 Tahun)</option>
                                            <option value="36">36 Bulan (3 Tahun)</option>
                                            <option value="48">48 Bulan (4 Tahun)</option>
                                            <option value="60">60 Bulan (5 Tahun)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                            Tujuan Pinjaman <span class="text-red-500">*</span>
                                        </label>
                                        <select name="tujuan_pinjaman" id="tujuan_pinjaman" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition" required>
                                            <option value="">-- Pilih Tujuan --</option>
                                            <option value="Modal Usaha">Modal Usaha</option>
                                            <option value="Pendidikan">Pendidikan</option>
                                            <option value="Renovasi">Renovasi</option>
                                            <option value="Kesehatan">Kesehatan/Darurat</option> ✅
                                            <option value="Pernikahan">Pernikahan</option>
                                            <option value="Konsumtif">Konsumtif</option>
                                            <option value="Investasi">Investasi</option>
                                        </select>
                                    </div>
                                    <!-- Pertanyaan Jaminan -->
                                    <div class="md:col-span-2 lg:col-span-3">
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            Apakah ada jaminan? <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" name="ada_jaminan" value="ya" class="peer sr-only" required>
                                                <div class="peer-checked:bg-emerald-500 peer-checked:text-white bg-white border-2 border-emerald-200 rounded-xl p-4 text-center transition-all hover:border-emerald-300 peer-checked:border-emerald-500 peer-checked:shadow-lg">
                                                    <svg class="w-8 h-8 mx-auto mb-2 text-emerald-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <p class="text-sm font-bold">Ada Jaminan</p>
                                                </div>
                                            </label>
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" name="ada_jaminan" value="tidak" class="peer sr-only" required>
                                                <div class="peer-checked:bg-rose-500 peer-checked:text-white bg-white border-2 border-rose-200 rounded-xl p-4 text-center transition-all hover:border-rose-300 peer-checked:border-rose-500 peer-checked:shadow-lg">
                                                    <svg class="w-8 h-8 mx-auto mb-2 text-rose-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <p class="text-sm font-bold">Tidak Ada</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Bagian Jaminan (Disembunyikan secara default) -->
                            <div id="jaminan-section" class="hidden bg-gradient-to-br from-amber-50 to-yellow-50 rounded-3xl p-6 shadow-lg border-2 border-amber-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            Jaminan <span class="text-red-500">*</span>
                                        </label>
                                        <select name="jaminan" id="jaminan" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" required>
                                            <option value="">-- Pilih Jaminan --</option>
                                            <option value="BPKB Motor">BPKB Motor</option>
                                            <option value="BPKB Mobil">BPKB Mobil</option>
                                            <option value="Sertifikat Tanah">Sertifikat Tanah</option>
                                            <option value="Sertifikat Rumah">Sertifikat Rumah</option>
                                            <option value="Slip Gaji">Slip Gaji</option>
                                            <option value="Deposito">Deposito</option>
                                            <option value="Emas">Emas</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div id="jaminan-lainnya-container" class="hidden">
                                        <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Jelaskan Jaminan Lainnya <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="jaminan_lainnya" id="jaminan-lainnya" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition" placeholder="Contoh: Sertifikat Kendaraan Roda 3">
                                    </div>
                                </div>
                            </div>
                            <!-- Riwayat Tunggakan Section -->
                            <div class="bg-gradient-to-br from-slate-50 to-sky-50 rounded-3xl p-6 shadow-lg border-2 border-slate-200">
                                <h5 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Riwayat Tunggakan <span class="text-red-500 ml-1">*</span>
                                </h5>
                                <p class="text-sm text-gray-600 mb-4">Apakah Anda pernah memiliki riwayat keterlambatan pembayaran pinjaman?</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label class="relative cursor-pointer group">
                                        <!-- CHANGED VALUE FROM "Tidak Pernah" TO "Tidak" -->
                                        <!-- REMOVED 'checked' AS INPUT HIDDEN WILL HANDLE THE VALUE -->
                                        <!-- KEPT 'disabled' AS PER REQUIREMENT -->
                                        <input type="radio" name="riwayat_tunggakan" value="Tidak" class="peer sr-only" disabled>
                                        <div class="peer-checked:bg-emerald-500 peer-checked:text-white bg-white border-2 border-emerald-200 rounded-xl p-4 text-center transition-all hover:border-emerald-300 peer-checked:border-emerald-500 peer-checked:shadow-lg">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-emerald-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-sm font-bold">1. Tidak</p>
                                            <p class="text-xs mt-1 peer-checked:text-white text-gray-600">Tidak ada tunggakan</p>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="riwayat_tunggakan" value="Pernah" class="peer sr-only" disabled>
                                        <div class="peer-checked:bg-amber-500 peer-checked:text-white bg-white border-2 border-amber-200 rounded-xl p-4 text-center transition-all hover:border-amber-300 peer-checked:border-amber-500 peer-checked:shadow-lg">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-amber-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-sm font-bold">2. Pernah</p>
                                            <p class="text-xs mt-1 peer-checked:text-white text-gray-600">Ada riwayat tunggakan</p>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group col-span-full"> <!-- Full width for the third option -->
                                        <input type="radio" name="riwayat_tunggakan" value="Sering" class="peer sr-only" disabled>
                                        <div class="peer-checked:bg-rose-500 peer-checked:text-white bg-white border-2 border-rose-200 rounded-xl p-4 text-center transition-all hover:border-rose-300 peer-checked:border-rose-500 peer-checked:shadow-lg">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-rose-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-sm font-bold">3. Sering</p>
                                            <p class="text-xs mt-1 peer-checked:text-white text-gray-600">Sering terjadi tunggakan</p>
                                        </div>
                                    </label>
                                </div>
                                <!-- ADDED HIDDEN INPUT TO SEND THE DEFAULT VALUE -->
                                <!-- This input will send the value "Tidak Pernah" to the server -->
                                <input type="hidden" name="riwayat_tunggakan" value="Tidak">
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="flex justify-between items-center gap-4 pt-6 border-t-2 border-gray-200">
                            <a href="{{ route('anggota.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-xl font-bold transition-all shadow-md">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Batal
                            </a>
                            <button type="submit" id="btn-submit" class="px-10 py-3 bg-gradient-to-r from-cyan-600 via-blue-600 to-blue-700 text-white text-lg font-bold rounded-xl shadow-2xl hover:from-cyan-700 hover:via-blue-700 hover:to-blue-800 transform hover:-translate-y-1 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" disabled>
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Elements
            const fotoKtpInput = document.getElementById('foto_ktp');
            const fotoSelfieInput = document.getElementById('foto_selfie_ktp');
            const ocrLoading = document.getElementById('ocr-loading');
            const ocrResult = document.getElementById('ocr-result');
            const ocrPreviewDisplay = document.getElementById('ocr-preview-display');
            const manualInputSection = document.getElementById('manual-input-section');
            const additionalDataSection = document.getElementById('additional-data-section');
            const financialDataSection = document.getElementById('financial-data-section');
            const loanDataSection = document.getElementById('loan-data-section');
            const btnEditManual = document.getElementById('btn-edit-manual');
            const btnCancelEdit = document.getElementById('btn-cancel-edit');
            const btnSaveManual = document.getElementById('btn-save-manual');
            const btnSubmit = document.getElementById('btn-submit');
            const lamaBekerjaInput = document.getElementById('lama_bekerja');
            const kerjaProgress = document.getElementById('kerja-progress');
            const kerjaValue = document.getElementById('kerja-value');
            
            // Jaminan Elements
            const adaJaminanRadio = document.querySelectorAll('input[name="ada_jaminan"]');
            const jaminanSection = document.getElementById('jaminan-section');
            const jaminanSelect = document.getElementById('jaminan');
            const jaminanLainnyaContainer = document.getElementById('jaminan-lainnya-container');
            const jaminanLainnyaInput = document.getElementById('jaminan-lainnya');
            
            let ocrData = {};
            let isOcrSuccess = false;

            // --- 1. LOGIC UTAMA: JAMINAN ---
            adaJaminanRadio.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'ya') {
                        // Tampilkan Section Jaminan
                        jaminanSection.classList.remove('hidden');
                        
                        // Set Required
                        jaminanSelect.setAttribute('required', 'required');
                    } else {
                        // Sembunyikan Section Jaminan
                        jaminanSection.classList.add('hidden');
                        
                        // Reset Value & Hapus Required
                        jaminanSelect.value = "";
                        jaminanSelect.removeAttribute('required');
                        
                        jaminanLainnyaContainer.classList.add('hidden');
                        jaminanLainnyaInput.value = "";
                        jaminanLainnyaInput.removeAttribute('required');
                    }
                });
            });

            // Logic dropdown "Lainnya"
            jaminanSelect.addEventListener('change', function() {
                if (this.value === 'Lainnya') {
                    jaminanLainnyaContainer.classList.remove('hidden');
                    jaminanLainnyaInput.setAttribute('required', 'required');
                } else {
                    jaminanLainnyaContainer.classList.add('hidden');
                    jaminanLainnyaInput.value = "";
                    jaminanLainnyaInput.removeAttribute('required');
                }
            });

            // --- 2. LOGIC SUBMIT FORM ---
            document.getElementById('formAnggota').addEventListener('submit', function(e) {
                // Cek status tempat tinggal (Rumah Orang Tua -> Milik Orang Tua)
                const statusTinggalInput = document.querySelector('input[name="status_tempat_tinggal"]:checked');
                if (statusTinggalInput && statusTinggalInput.value === 'Rumah Orang Tua') {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'status_tempat_tinggal';
                    hiddenInput.value = 'Milik Orang Tua';
                    this.appendChild(hiddenInput);
                    statusTinggalInput.disabled = true; 
                }

                // Cek Jaminan
                const adaJaminan = document.querySelector('input[name="ada_jaminan"]:checked');
                
                // Jika user memilih "Tidak Ada Jaminan", pastikan input jaminan benar-benar kosong agar tidak error validasi server
                if (adaJaminan && adaJaminan.value === 'tidak') {
                    // Hapus atribut name sementara agar tidak terkirim ke server (jika server validasi required)
                    // Atau biarkan kosong (value="") jika server mengizinkan nullable
                    jaminanSelect.value = ""; 
                    jaminanLainnyaInput.value = "";
                }

                // Validasi Client-Side Tambahan (Optional)
                if (adaJaminan && adaJaminan.value === 'ya' && jaminanSelect.value === 'Lainnya' && !jaminanLainnyaInput.value.trim()) {
                    e.preventDefault();
                    showNotification('❌ Harap isi keterangan jaminan lainnya.', 'error');
                    jaminanLainnyaInput.focus();
                    return;
                }

                // UI Loading
                console.log('📤 Form submitting...');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Mengirim...';
            });

            // --- 3. LOGIC LAMA BEKERJA (PROGRESS BAR) ---
            if (lamaBekerjaInput) {
                lamaBekerjaInput.addEventListener('input', function() {
                    const value = parseFloat(this.value) || 0;
                    let percentage = Math.min(value * 10, 100);
                    kerjaProgress.style.width = `${percentage}%`;
                    
                    if (value < 1) {
                        kerjaProgress.style.background = 'linear-gradient(to right, #ff6b6b, #ee5a24)';
                    } else if (value < 3) {
                        kerjaProgress.style.background = 'linear-gradient(to right, #ffd32a, #ff9f43)';
                    } else {
                        kerjaProgress.style.background = 'linear-gradient(to right, #00d2d3, #00a8ff)';
                    }
                    kerjaValue.textContent = value === 0 ? '0 tahun' : `${value} tahun`;
                });
                
                // Init value
                if (lamaBekerjaInput.value) {
                    lamaBekerjaInput.dispatchEvent(new Event('input'));
                }
            }

            // --- 4. OCR LOGIC (KTP & SELFIE) ---
            fotoKtpInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                
                // Preview
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('img-ktp').src = e.target.result;
                    document.getElementById('preview-ktp').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                
                startOCRProcess(file);
            });

            fotoSelfieInput.addEventListener('change', function () {
                if (this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        document.getElementById('img-selfie').src = e.target.result;
                        document.getElementById('preview-selfie').classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // OCR Function
            function startOCRProcess(file) {
                console.log('🔄 Starting OCR process...');
                ocrLoading.classList.remove('hidden');
                ocrResult.classList.add('hidden');
                additionalDataSection.classList.add('hidden');
                financialDataSection.classList.add('hidden');
                loanDataSection.classList.add('hidden');
                btnSubmit.disabled = true;

                const formData = new FormData();
                formData.append('file', file);

                fetch('http://127.0.0.1:8001/extract-ktp', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(result => {
                    console.log('✅ OCR Result:', result);
                    ocrLoading.classList.add('hidden');

                    const titleEl = document.getElementById('ocr-status-title');
                    const iconEl = document.getElementById('ocr-status-icon');
                    const svgPath = document.getElementById('ocr-status-svg');

                    if (result.success && result.data) {
                        // Success State
                        if(titleEl) {
                            titleEl.innerText = "Data KTP Terdeteksi";
                            titleEl.className = "text-xl font-bold text-cyan-800";
                        }
                        if(iconEl) iconEl.className = "w-10 h-10 bg-green-500 rounded-full flex items-center justify-center";
                        if(svgPath) svgPath.setAttribute('d', "M5 13l4 4L19 7");

                        if (!result.data.nik || result.data.nik.length !== 16) {
                            throw new Error('NIK tidak valid (harus 16 digit)');
                        }

                        ocrData = result.data;
                        isOcrSuccess = true;
                        populatePreview(ocrData);
                        populateManualInputs(ocrData);

                        ocrResult.classList.remove('hidden');
                        ocrPreviewDisplay.classList.remove('hidden');
                        manualInputSection.classList.add('hidden');
                        btnEditManual.classList.remove('hidden');

                        additionalDataSection.classList.remove('hidden');
                        financialDataSection.classList.remove('hidden');
                        loanDataSection.classList.remove('hidden');
                        btnSubmit.disabled = false;
                        showNotification('✅ Data KTP berhasil dibaca!', 'success');
                    } else {
                        throw new Error(result.message || 'Gagal membaca KTP');
                    }
                })
                .catch(err => {
                    console.error('❌ OCR Error:', err);
                    ocrLoading.classList.add('hidden');

                    // Error State
                    const titleEl = document.getElementById('ocr-status-title');
                    const iconEl = document.getElementById('ocr-status-icon');
                    const svgPath = document.getElementById('ocr-status-svg');

                    if(titleEl) {
                        titleEl.innerText = "Gagal Mengambil Data KTP";
                        titleEl.className = "text-xl font-bold text-red-600";
                    }
                    if(iconEl) iconEl.className = "w-10 h-10 bg-red-500 rounded-full flex items-center justify-center";
                    if(svgPath) svgPath.setAttribute('d', "M6 18L18 6M6 6l12 12"); 

                    showNotification('Gagal mengambil data KTP', 'error');

                    ocrResult.classList.remove('hidden');
                    manualInputSection.classList.remove('hidden');
                    ocrPreviewDisplay.classList.add('hidden');
                    btnEditManual.classList.add('hidden');

                    additionalDataSection.classList.remove('hidden');
                    financialDataSection.classList.remove('hidden');
                    loanDataSection.classList.remove('hidden');
                    btnSubmit.disabled = false;
                    isOcrSuccess = false;
                });
            }

            // --- 5. HELPER FUNCTIONS ---
            btnEditManual.addEventListener('click', function() {
                manualInputSection.classList.remove('hidden');
                ocrPreviewDisplay.classList.add('hidden');
                btnEditManual.classList.add('hidden');
                manualInputSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });

            btnCancelEdit.addEventListener('click', function() {
                if (isOcrSuccess) {
                    manualInputSection.classList.add('hidden');
                    ocrPreviewDisplay.classList.remove('hidden');
                    btnEditManual.classList.remove('hidden');
                    populateManualInputs(ocrData);
                }
            });

            btnSaveManual.addEventListener('click', function() {
                const manualData = {
                    nik: document.getElementById('input-nik').value,
                    nama: document.getElementById('input-nama').value,
                    tempat_lahir: document.getElementById('input-tempat-lahir').value,
                    tanggal_lahir: document.getElementById('input-tanggal-lahir').value,
                    jenis_kelamin: document.getElementById('input-jenis-kelamin').value,
                    alamat: document.getElementById('input-alamat').value,
                    rt: document.getElementById('input-rt').value,
                    rw: document.getElementById('input-rw').value,
                    kelurahan: document.getElementById('input-kelurahan').value,
                    kecamatan: document.getElementById('input-kecamatan').value,
                    kabupaten_kota: document.getElementById('input-kabupaten-kota').value,
                    provinsi: document.getElementById('input-provinsi').value,
                    agama: document.getElementById('input-agama').value,
                    status_perkawinan: document.getElementById('input-status-perkawinan').value,
                    pekerjaan: document.getElementById('input-pekerjaan').value,
                    gol_darah: document.getElementById('input-gol-darah').value
                };

                if (!manualData.nik || !manualData.nama || !manualData.jenis_kelamin) {
                    showNotification('❌ NIK, Nama, dan Jenis Kelamin wajib diisi!', 'error');
                    return;
                }
                ocrData = manualData;
                populatePreview(manualData);
                manualInputSection.classList.add('hidden');
                ocrPreviewDisplay.classList.remove('hidden');
                btnEditManual.classList.remove('hidden');
                showNotification('✅ Data berhasil diperbarui!', 'success');
            });

            function populatePreview(data) {
                document.getElementById('preview-nik').textContent = data.nik || '-';
                document.getElementById('preview-nama').textContent = data.nama || '-';
                document.getElementById('preview-ttl').textContent = [data.tempat_lahir, data.tanggal_lahir].filter(Boolean).join(' / ') || '-';
                document.getElementById('preview-jk').textContent = data.jenis_kelamin === 'L' ? 'Laki-laki' : (data.jenis_kelamin === 'P' ? 'Perempuan' : data.jenis_kelamin || '-');
                document.getElementById('preview-alamat').textContent = data.alamat || '-';
                document.getElementById('preview-rtrw').textContent = (data.rt || '000') + '/' + (data.rw || '000');
                document.getElementById('preview-keldesa').textContent = data.kelurahan || data.desa || '-';
                document.getElementById('preview-kecamatan').textContent = data.kecamatan || '-';
                document.getElementById('preview-kabkota').textContent = data.kabupaten_kota || '-';
                document.getElementById('preview-provinsi').textContent = data.provinsi || '-';
                document.getElementById('preview-agama').textContent = data.agama || '-';
                document.getElementById('preview-status').textContent = data.status_perkawinan || data.status || '-';
                document.getElementById('preview-pekerjaan').textContent = data.pekerjaan || '-';
                document.getElementById('preview-goldarah').textContent = data.gol_darah || '-';
            }

            function populateManualInputs(data) {
                document.getElementById('input-nik').value = data.nik || '';
                document.getElementById('input-nama').value = data.nama || '';
                document.getElementById('input-tempat-lahir').value = data.tempat_lahir || '';
                document.getElementById('input-tanggal-lahir').value = data.tanggal_lahir || '';
                document.getElementById('input-jenis-kelamin').value = data.jenis_kelamin || '';
                document.getElementById('input-alamat').value = data.alamat || '';
                document.getElementById('input-rt').value = data.rt || '';
                document.getElementById('input-rw').value = data.rw || '';
                document.getElementById('input-kelurahan').value = data.kelurahan || data.desa || '';
                document.getElementById('input-kecamatan').value = data.kecamatan || '';
                document.getElementById('input-kabupaten-kota').value = data.kabupaten_kota || '';
                document.getElementById('input-provinsi').value = data.provinsi || '';
                document.getElementById('input-agama').value = data.agama || '';
                document.getElementById('input-status-perkawinan').value = data.status_perkawinan || data.status || '';
                document.getElementById('input-pekerjaan').value = data.pekerjaan || '';
                document.getElementById('input-gol-darah').value = data.gol_darah || '';
            }

            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-bounce`;
                notification.innerHTML = message;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 3000);
            }
        });
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        input[type="radio"]:checked + div {
            transform: translateY(-2px);
        }
        .peer:checked ~ div {
            box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.35);
        }
        /* Enhanced status tempat tinggal cards */
        .peer:checked ~ div {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -5px rgba(139, 92, 246, 0.4), 0 10px 10px -5px rgba(0,0,0,0.05);
        }
        .group:hover .peer:not(:checked) ~ div {
            border-color: #c084fc;
            transform: translateY(-1px);
        }
        /* Enhanced lama bekerja field */
        .group:focus-within .peer:not(:checked) ~ div {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</x-app-layout>