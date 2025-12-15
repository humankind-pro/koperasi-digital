<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Manajemen Data Nasabah (Super Admin)</h2>
                    </div>

                    {{-- Pesan Sukses --}}
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode/Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK & Kontak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th> {{-- KOLOM BARU --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($anggotas as $anggota)
                                    <tr class="hover:bg-gray-50">
                                        {{-- Kode & Nama --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-indigo-600">{{ $anggota->kode_anggota }}</div>
                                            <div class="text-sm font-medium text-gray-900">{{ $anggota->nama }}</div>
                                        </td>

                                        {{-- NIK & Kontak --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $anggota->nik }}</div>
                                            <div class="text-xs text-gray-500">{{ $anggota->nomor_telepon }}</div>
                                        </td>

                                        {{-- Pekerjaan --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $anggota->pekerjaan ?? '-' }}
                                            <div class="text-xs text-green-600 font-bold">
                                                Gaji: Rp {{ number_format($anggota->pendapatan_bulanan, 0, ',', '.') }}
                                            </div>
                                        </td>

                                        {{-- DOKUMEN (FITUR BARU) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button 
                                                onclick="showDokumenModal(
                                                    '{{ $anggota->nama }}', 
                                                    '{{ asset('storage/' . $anggota->foto_ktp) }}', 
                                                    '{{ asset('storage/' . $anggota->foto_selfie_ktp) }}'
                                                )"
                                                class="inline-flex items-center px-3 py-1 bg-cyan-100 text-cyan-700 rounded-full text-xs font-bold hover:bg-cyan-200 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Lihat Foto
                                            </button>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($anggota->status == 'aktif' || $anggota->status == 'disetujui')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @elseif($anggota->status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($anggota->status) }}</span>
                                            @endif
                                        </td>

                                        {{-- Aksi Edit/Hapus --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('superadmin.anggota.edit', $anggota->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            
                                            <form action="{{ route('superadmin.anggota.destroy', $anggota->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data nasabah ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data nasabah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $anggotas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL LIHAT DOKUMEN (KTP & SELFIE) --}}
    {{-- ======================================================= --}}
    <div id="dokumenModal" class="fixed inset-0 bg-gray-900 bg-opacity-80 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50 p-4 backdrop-blur-sm">
        <div class="relative bg-white w-full max-w-4xl mx-auto rounded-xl shadow-2xl overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="flex justify-between items-center p-5 border-b bg-gray-50">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Dokumen Nasabah</h3>
                    <p id="modal-nama-nasabah" class="text-sm text-gray-500 font-medium"></p>
                </div>
                <button onclick="closeDokumenModal()" class="text-gray-400 hover:text-red-600 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- Isi Modal (Gambar) --}}
            <div class="p-6 bg-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kolom KTP --}}
                    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                        <div class="flex items-center justify-between mb-3 border-b pb-2">
                            <h4 class="font-bold text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                Foto KTP
                            </h4>
                            <a id="link-ktp" href="#" target="_blank" class="text-xs text-blue-600 hover:underline">Buka Ukuran Asli</a>
                        </div>
                        <div class="aspect-video w-full bg-gray-200 rounded-md overflow-hidden flex items-center justify-center">
                            <img id="img-ktp" src="" alt="KTP" class="w-full h-full object-contain hover:scale-105 transition duration-300 cursor-zoom-in" onclick="window.open(this.src, '_blank')">
                        </div>
                    </div>

                    {{-- Kolom Selfie --}}
                    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                        <div class="flex items-center justify-between mb-3 border-b pb-2">
                            <h4 class="font-bold text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Foto Selfie + KTP
                            </h4>
                            <a id="link-selfie" href="#" target="_blank" class="text-xs text-blue-600 hover:underline">Buka Ukuran Asli</a>
                        </div>
                        <div class="aspect-video w-full bg-gray-200 rounded-md overflow-hidden flex items-center justify-center">
                            <img id="img-selfie" src="" alt="Selfie" class="w-full h-full object-contain hover:scale-105 transition duration-300 cursor-zoom-in" onclick="window.open(this.src, '_blank')">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-4 bg-gray-50 border-t flex justify-end">
                <button onclick="closeDokumenModal()" class="px-6 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-medium shadow-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        function showDokumenModal(nama, urlKtp, urlSelfie) {
            // Set Data ke Modal
            document.getElementById('modal-nama-nasabah').textContent = nama;
            
            // Set Image Sources
            const imgKtp = document.getElementById('img-ktp');
            const imgSelfie = document.getElementById('img-selfie');
            
            imgKtp.src = urlKtp;
            imgSelfie.src = urlSelfie;

            // Set Links untuk buka tab baru
            document.getElementById('link-ktp').href = urlKtp;
            document.getElementById('link-selfie').href = urlSelfie;

            // Tampilkan Modal
            document.getElementById('dokumenModal').classList.remove('hidden');
        }

        function closeDokumenModal() {
            document.getElementById('dokumenModal').classList.add('hidden');
        }

        // Tutup jika klik di luar modal (overlay)
        window.onclick = function(event) {
            const modal = document.getElementById('dokumenModal');
            if (event.target == modal) {
                closeDokumenModal();
            }
        }
    </script>
</x-app-layout>