<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Validasi Pengajuan Pinjaman
                    </h3>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nasabah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengajuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenor</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuanPinjaman as $pinjaman)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $pinjaman->anggota->nama }}</div>
                                            <div class="text-sm text-gray-500">{{ $pinjaman->anggota->no_ktp }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-indigo-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</div>
                                            <div class="text-xs text-gray-500">Gaji: Rp {{ number_format($pinjaman->anggota->pendapatan_bulanan, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $pinjaman->lama_angsuran }} Bulan
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                onclick="openReviewModal({{ json_encode($pinjaman) }}, {{ json_encode($pinjaman->anggota) }})"
                                                class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                                Review & AI Check
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada pengajuan pinjaman yang perlu divalidasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pengajuanPinjaman->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- ======================================================= --}}
{{-- MODAL REVIEW & AI (POP-UP) --}}
{{-- ======================================================= --}}
<div id="reviewModal" 
     class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50 p-4"
     data-tolak-url="{{ route('admin.validasi.pinjaman.tolak', ['pinjaman' => '__ID__']) }}"
     data-setujui-url="{{ route('admin.validasi.pinjaman.setujui', ['pinjaman' => '__ID__']) }}">
     
    <div class="relative bg-white w-full max-w-lg mx-auto rounded-xl shadow-2xl">
        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b bg-gray-50 rounded-t-xl">
            <h3 class="text-lg font-bold text-gray-900">Analisis Pengajuan Pinjaman</h3>
            <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6">
            {{-- Data Singkat --}}
            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div>
                    <label class="block text-gray-500 text-xs uppercase">Nasabah</label>
                    <span id="modal-nama" class="font-bold text-gray-800 text-lg"></span>
                </div>
                <div>
                    <label class="block text-gray-500 text-xs uppercase">Jumlah Pengajuan</label>
                    <span id="modal-jumlah" class="font-bold text-indigo-600 text-lg"></span>
                </div>
                <div>
                    <label class="block text-gray-500 text-xs uppercase">Pendapatan</label>
                    <span id="modal-gaji" class="font-medium text-gray-800"></span>
                </div>
                <div>
                    <label class="block text-gray-500 text-xs uppercase">Tenor</label>
                    <span id="modal-tenor" class="font-medium text-gray-800"></span>
                </div>
            </div>

            {{-- AREA AI (SISTEM CERDAS) --}}
            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-indigo-200 rounded-full opacity-20"></div>
                
                <h4 class="font-bold text-indigo-800 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Sistem Cerdas (AI)
                </h4>
                
                <input type="hidden" id="modal-pinjaman-id">

                {{-- State 1: Tombol Start --}}
                <div id="ai-start">
                    <button onclick="jalankanAI()" class="w-full py-2 bg-indigo-600 text-white rounded text-sm font-medium hover:bg-indigo-700 transition shadow">
                        Cek Kelayakan Sekarang
                    </button>
                    <p class="text-xs text-indigo-400 mt-2 text-center">Sistem akan menganalisis data gaji, tenor, & riwayat kredit.</p>
                </div>

                {{-- State 2: Loading --}}
                <div id="ai-loading" class="hidden text-center py-2">
                    <svg class="animate-spin h-5 w-5 text-indigo-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-indigo-600 text-xs font-semibold">Sedang Menganalisis...</span>
                </div>

                {{-- State 3: Hasil --}}
                <div id="ai-result" class="hidden">
                    <div class="flex justify-between items-end border-b border-indigo-200 pb-2 mb-2">
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Rekomendasi</p>
                            <p id="ai-rekomendasi" class="text-2xl font-extrabold text-gray-800">--</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase">Keyakinan</p>
                            <p id="ai-skor" class="text-lg font-bold text-gray-600">--%</p>
                        </div>
                    </div>
                    <button onclick="resetAI()" class="text-xs text-indigo-500 hover:text-indigo-700 underline">Analisis Ulang</button>
                </div>
            </div>

            {{-- Form Input Persetujuan --}}
            <form id="form-setujui" method="POST" action="">
                @csrf @method('PATCH')
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Disetujui (Rp)</label>
                <input type="number" name="jumlah_disetujui" id="input-disetujui" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mb-4" required>
                
                <div class="flex space-x-3 justify-end">
                    <button type="button" onclick="closeReviewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm font-medium">Batal</button>
                    
                    {{-- Form Tolak (Hidden Action) --}}
                    <button type="button" onclick="submitTolak()" class="px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium border border-red-200">
                        Tolak
                    </button>

                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium shadow-sm">
                        Setujui Pinjaman
                    </button>
                </div>
            </form>
            
            {{-- Form Tolak Terpisah (Untuk submit via JS) --}}
            <form id="form-tolak" method="POST" action="" class="hidden">@csrf @method('PATCH')</form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('reviewModal');
    const formSetujui = document.getElementById('form-setujui');
    const formTolak = document.getElementById('form-tolak');
    
    // URL Templates
    const tolakUrlTemplate = modal.dataset.tolakUrl;
    const setujuiUrlTemplate = modal.dataset.setujuiUrl;

    function openReviewModal(pinjaman, anggota) {
        // Isi Data Modal
        document.getElementById('modal-nama').textContent = anggota.nama;
        document.getElementById('modal-jumlah').textContent = 'Rp ' + parseInt(pinjaman.jumlah_pinjaman).toLocaleString('id-ID');
        document.getElementById('modal-gaji').textContent = 'Rp ' + parseInt(anggota.pendapatan_bulanan).toLocaleString('id-ID');
        document.getElementById('modal-tenor').textContent = pinjaman.lama_angsuran + ' Bulan';
        
        // Isi input approval dengan jumlah request awal
        document.getElementById('input-disetujui').value = pinjaman.jumlah_pinjaman;
        
        // Set ID untuk AI
        document.getElementById('modal-pinjaman-id').value = pinjaman.id;

        // Update URL Forms
        formSetujui.action = setujuiUrlTemplate.replace('__ID__', pinjaman.id);
        formTolak.action = tolakUrlTemplate.replace('__ID__', pinjaman.id);

        resetAI();
        modal.classList.remove('hidden');
    }

    function closeReviewModal() {
        modal.classList.add('hidden');
    }

    function submitTolak() {
        if(confirm('Apakah Anda yakin ingin menolak pengajuan ini?')) {
            formTolak.submit();
        }
    }

    // ==========================================
    // LOGIKA AI (PYTHON BRIDGE)
    // ==========================================
    function resetAI() {
        document.getElementById('ai-start').classList.remove('hidden');
        document.getElementById('ai-loading').classList.add('hidden');
        document.getElementById('ai-result').classList.add('hidden');
    }

    function jalankanAI() {
        const id = document.getElementById('modal-pinjaman-id').value;
        
        // UI State: Loading
        document.getElementById('ai-start').classList.add('hidden');
        document.getElementById('ai-loading').classList.remove('hidden');

        // Panggil Controller Laravel
        fetch(`/admin/validasi/cek-ai/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('ai-loading').classList.add('hidden');
                document.getElementById('ai-result').classList.remove('hidden');
                
                if (data.status === 'error') {
                    alert('Error: ' + data.pesan);
                    resetAI();
                    return;
                }

                // Tampilkan Hasil
                const rekText = document.getElementById('ai-rekomendasi');
                rekText.textContent = data.rekomendasi;
                document.getElementById('ai-skor').textContent = data.skor + '%';

                // Warna Hasil
                if (data.rekomendasi === 'Layak') {
                    rekText.className = 'text-2xl font-extrabold text-green-600';
                } else {
                    rekText.className = 'text-2xl font-extrabold text-red-600';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghubungi server AI');
                resetAI();
            });
    }
</script>