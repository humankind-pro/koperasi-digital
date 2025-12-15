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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profil Risiko</th>
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
                                            <div class="text-sm text-gray-500">{{ $pinjaman->anggota->nik ?? $pinjaman->anggota->no_ktp }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($pinjaman->anggota->kelayakan == 'Layak')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Layak (Low Risk)
                                                </span>
                                            @elseif($pinjaman->anggota->kelayakan == 'Dipertimbangkan')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Dipertimbangkan
                                                </span>
                                            @elseif($pinjaman->anggota->kelayakan == 'Tidak Layak')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    High Risk
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-indigo-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</div>
                                            <div class="text-xs text-gray-500">Gaji: Rp {{ number_format($pinjaman->anggota->pendapatan_bulanan, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $pinjaman->lama_angsuran ?? $pinjaman->tenor_bulan }} Bulan
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                onclick="openReviewModal({{ json_encode($pinjaman) }}, {{ json_encode($pinjaman->anggota) }})"
                                                class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                                Validasi Peminjaman
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
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

{{-- MODAL VALIDASI --}}
<div id="reviewModal" 
     class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50 p-4"
     data-tolak-url="{{ route('admin.validasi.pinjaman.tolak', ['pinjaman' => '__ID__']) }}"
     data-setujui-url="{{ route('admin.validasi.pinjaman.setujui', ['pinjaman' => '__ID__']) }}">
     
    <div class="relative bg-white w-full max-w-lg mx-auto rounded-xl shadow-2xl">
        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b bg-gray-50 rounded-t-xl">
            <h3 class="text-lg font-bold text-gray-900">Validasi Peminjaman</h3>
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
                    <label class="block text-gray-500 text-xs uppercase">Status Kelayakan</label>
                    <span id="modal-status-kelayakan" class="font-bold"></span>
                </div>
                <div>
                    <label class="block text-gray-500 text-xs uppercase">Jumlah Pengajuan</label>
                    <span id="modal-jumlah" class="font-bold text-indigo-600 text-lg"></span>
                </div>
                <div>
                    <label class="block text-gray-500 text-xs uppercase">Pendapatan</label>
                    <span id="modal-gaji" class="font-medium text-gray-800"></span>
                </div>
            </div>

            {{-- Form Input Persetujuan --}}
            <form id="form-setujui" method="POST" action="">
                @csrf @method('PATCH')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <label class="block text-sm font-medium text-yellow-800 mb-1">Jumlah yang Disetujui (Rp)</label>
                    <input type="number" name="jumlah_disetujui" id="input-disetujui" class="w-full border-yellow-400 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 font-bold" required>
                    <p class="text-xs text-yellow-600 mt-1">Anda dapat mengubah jumlah ini jika dirasa terlalu besar.</p>
                </div>
                
                <div class="flex space-x-3 justify-end pt-2 border-t">
                    <button type="button" onclick="closeReviewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm font-medium">Batal</button>
                    
                    <button type="button" onclick="submitTolak()" class="px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium border border-red-200">
                        Tolak Peminjaman
                    </button>

                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium shadow-sm">
                        Setujui & Cairkan
                    </button>
                </div>
            </form>
            
            {{-- Form Tolak Hidden --}}
            <form id="form-tolak" method="POST" action="" class="hidden">@csrf @method('PATCH')</form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('reviewModal');
    const formSetujui = document.getElementById('form-setujui');
    const formTolak = document.getElementById('form-tolak');
    const modalStatusKelayakan = document.getElementById('modal-status-kelayakan');
    
    // URL Templates
    const tolakUrlTemplate = modal.dataset.tolakUrl;
    const setujuiUrlTemplate = modal.dataset.setujuiUrl;

    function openReviewModal(pinjaman, anggota) {
        // Isi Data Modal
        document.getElementById('modal-nama').textContent = anggota.nama;
        document.getElementById('modal-jumlah').textContent = 'Rp ' + parseInt(pinjaman.jumlah_pinjaman).toLocaleString('id-ID');
        document.getElementById('modal-gaji').textContent = 'Rp ' + parseInt(anggota.pendapatan_bulanan).toLocaleString('id-ID');
        
        // Badge Kelayakan (Static Data from Registration)
        modalStatusKelayakan.className = "font-bold px-2 py-0.5 rounded text-sm";
        if (anggota.kelayakan === 'Layak') {
            modalStatusKelayakan.textContent = "LAYAK";
            modalStatusKelayakan.classList.add('bg-green-100', 'text-green-800');
        } else if (anggota.kelayakan === 'Dipertimbangkan') {
            modalStatusKelayakan.textContent = "DIPERTIMBANGKAN";
            modalStatusKelayakan.classList.add('bg-yellow-100', 'text-yellow-800');
        } else if (anggota.kelayakan === 'Tidak Layak') {
            modalStatusKelayakan.textContent = "TIDAK LAYAK";
            modalStatusKelayakan.classList.add('bg-red-100', 'text-red-800');
        } else {
            modalStatusKelayakan.textContent = "-";
            modalStatusKelayakan.classList.add('bg-gray-100', 'text-gray-800');
        }

        // Isi input approval dengan jumlah request awal
        document.getElementById('input-disetujui').value = pinjaman.jumlah_pinjaman;
        
        // Update URL Forms
        formSetujui.action = setujuiUrlTemplate.replace('__ID__', pinjaman.id);
        formTolak.action = tolakUrlTemplate.replace('__ID__', pinjaman.id);

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
</script>