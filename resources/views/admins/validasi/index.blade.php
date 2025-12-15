<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Validasi Pengajuan Anggota Baru
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anggota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Kontak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Score</th> <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diajukan Oleh</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuanAnggota as $anggota)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $anggota->nama }}</div>
                                            <div class="text-sm text-gray-500">NIK: {{ $anggota->nik ?? $anggota->no_ktp }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $anggota->nomor_telepon }}</div>
                                            <div class="text-xs text-gray-500 truncate w-32" title="{{ $anggota->alamat }}">
                                                {{ $anggota->alamat }}
                                            </div>
                                        </td>
                                        {{-- KOLOM BADGE KELAYAKAN --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($anggota->kelayakan == 'Layak')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                    ✅ Layak
                                                </span>
                                            @elseif($anggota->kelayakan == 'Dipertimbangkan')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    ⚠️ Dipertimbangkan
                                                </span>
                                            @elseif($anggota->kelayakan == 'Tidak Layak')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                    ❌ Tidak Layak
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $anggota->dibuatOleh->name ?? 'System' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                type="button"
                                                class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none shadow-sm"
                                                onclick="showInfoModal({{ json_encode($anggota) }})">
                                                Detail & Validasi
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="mt-2 text-sm font-medium">Tidak ada pengajuan anggota baru.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pengajuanAnggota->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- MODAL INFO NASABAH --}}
<div id="infoModal" 
     class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50 p-4"
     data-tolak-url-template="{{ route('admin.validasi.nasabah.tolak', ['anggota' => '__ID__']) }}"
     data-setujui-url-template="{{ route('admin.validasi.nasabah.setujui', ['anggota' => '__ID__']) }}">
     
  <div class="relative bg-white w-full max-w-2xl mx-auto rounded-lg shadow-xl">
    
    <div class="flex justify-between items-center p-5 border-b rounded-t-lg bg-gray-50">
      <h3 class="text-xl font-bold text-gray-900">Detail Calon Anggota</h3>
      <button onclick="closeInfoModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>
    
    <div class="p-6">
        {{-- AREA HASIL AI DI DALAM MODAL --}}
        <div id="modal-ai-badge" class="mb-6 p-4 rounded-lg border flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Hasil Analisis AI</p>
                <p id="modal-kelayakan-text" class="text-lg font-bold"></p>
            </div>
            <div id="modal-ai-icon" class="text-3xl"></div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Nama Lengkap</dt>
                <dd class="mt-1 font-bold text-gray-900" id="modal-nama"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">NIK</dt>
                <dd class="mt-1 font-bold text-gray-900" id="modal-no_ktp"></dd>
            </div>
            <div class="col-span-2">
                <dt class="font-medium text-gray-500">Alamat</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-alamat"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">No. Telepon</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-nomor_telepon"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Pekerjaan</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-pekerjaan"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Pendapatan Bulanan</dt>
                <dd class="mt-1 font-bold text-green-600" id="modal-pendapatan"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Tanggal Daftar</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-tanggal"></dd>
            </div>
        </dl>
    </div>
    
    <div class="flex items-center p-6 space-x-3 border-t border-gray-200 rounded-b-lg justify-end bg-gray-50">
        <button onclick="closeInfoModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium shadow-sm">
            Tutup
        </button>
        
        <form id="form-tolak" method="POST" action="" class="inline-block">
            @csrf @method('PATCH')
            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-200 rounded-md hover:bg-red-200" onclick="return confirm('Yakin ingin menolak anggota ini?')">
                Tolak
            </button>
        </form>

        <form id="form-setujui" method="POST" action="" class="inline-block">
            @csrf @method('PATCH')
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 shadow-sm" onclick="return confirm('Setujui anggota ini? Pinjaman awal akan otomatis dibuat.')">
                Setujui Anggota
            </button>
        </form>
    </div>
  </div>
</div>

<script>
    const infoModal = document.getElementById('infoModal');
    const tolakUrlTemplate = infoModal.dataset.tolakUrlTemplate;
    const setujuiUrlTemplate = infoModal.dataset.setujuiUrlTemplate;
    const tolakForm = document.getElementById('form-tolak');
    const setujuiForm = document.getElementById('form-setujui');

    // Element untuk Badge AI di Modal
    const modalAiBadge = document.getElementById('modal-ai-badge');
    const modalKelayakanText = document.getElementById('modal-kelayakan-text');
    const modalAiIcon = document.getElementById('modal-ai-icon');

    function showInfoModal(anggota) {
        // Isi Data Modal
        document.getElementById('modal-nama').textContent = anggota.nama || '-';
        document.getElementById('modal-no_ktp').textContent = anggota.nik || anggota.no_ktp || '-';
        document.getElementById('modal-alamat').textContent = anggota.alamat || '-';
        document.getElementById('modal-nomor_telepon').textContent = anggota.nomor_telepon || '-';
        document.getElementById('modal-pekerjaan').textContent = anggota.pekerjaan || '-';
        
        document.getElementById('modal-pendapatan').textContent = anggota.pendapatan_bulanan ? 
            'Rp ' + parseInt(anggota.pendapatan_bulanan).toLocaleString('id-ID') : '-';
            
        document.getElementById('modal-tanggal').textContent = anggota.created_at ? 
            new Date(anggota.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';

        // === LOGIKA WARNA BADGE AI DI MODAL ===
        // Reset Kelas Warna
        modalAiBadge.className = "mb-6 p-4 rounded-lg border flex items-center justify-between"; 
        
        if (anggota.kelayakan === 'Layak') {
            modalAiBadge.classList.add('bg-green-50', 'border-green-200', 'text-green-800');
            modalKelayakanText.textContent = "✅ LAYAK";
            modalAiIcon.textContent = "😊";
        } else if (anggota.kelayakan === 'Dipertimbangkan') {
            modalAiBadge.classList.add('bg-yellow-50', 'border-yellow-200', 'text-yellow-800');
            modalKelayakanText.textContent = "⚠️ DIPERTIMBANGKAN";
            modalAiIcon.textContent = "🤔";
        } else if (anggota.kelayakan === 'Tidak Layak') {
            modalAiBadge.classList.add('bg-red-50', 'border-red-200', 'text-red-800');
            modalKelayakanText.textContent = "❌ TIDAK LAYAK";
            modalAiIcon.textContent = "🛑";
        } else {
            modalAiBadge.classList.add('bg-gray-50', 'border-gray-200', 'text-gray-800');
            modalKelayakanText.textContent = "⏳ PENDING / BELUM DIANALISIS";
            modalAiIcon.textContent = "Waiting";
        }

        // Update URL Action Form
        tolakForm.action = tolakUrlTemplate.replace('__ID__', anggota.id);
        setujuiForm.action = setujuiUrlTemplate.replace('__ID__', anggota.id);

        // Tampilkan Modal
        infoModal.classList.remove('hidden');
    }

    function closeInfoModal() {
        infoModal.classList.add('hidden');
    }
</script>