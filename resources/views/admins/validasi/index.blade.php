<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Validasi Pengajuan Anggota Baru
                    </h3>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div class="flex items-center pb-2 border-b-2">
                            <div class="w-1/3"><span class="text-xs font-semibold text-gray-500 uppercase">Nama Anggota</span></div>
                            <div class="w-1/3"><span class="text-xs font-semibold text-gray-500 uppercase">Diajukan Oleh (Karyawan)</span></div>
                            <div class="w-1/3 text-right"><span class="text-xs font-semibold text-gray-500 uppercase">Aksi</span></div>
                        </div>

                        @forelse ($pengajuanAnggota as $anggota)
                            <div class="flex items-center py-3 border-b">
                                <div class="w-1/3">
                                    <p class="font-semibold text-gray-800">{{ $anggota->nama }}</p>
                                    <p class="text-sm text-gray-500">{{ $anggota->no_ktp }}</p>
                                </div>
                                <div class="w-1/3">
                                    <p class="text-sm text-gray-600">{{ $anggota->dibuatOleh->name ?? 'N/A' }}</p>
                                </div>
                                <div class="w-1/3 text-right space-x-2">
                                    {{-- Tombol Info & Tindakan (Tidak Berubah) --}}
                                    <button
                                        type="button"
                                        class="px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded hover:bg-blue-600"
                                        onclick="showInfoModal({{ json_encode($anggota) }})">
                                        Info & Tindakan
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Tidak ada pengajuan anggota baru yang perlu divalidasi.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- ======================================================= --}}
{{-- MODAL INFO (DENGAN TAMPILAN LEBIH MODERN) --}}
{{-- ======================================================= --}}
<div id="infoModal" 
     class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50 p-4"
     data-tolak-url-template="{{ route('admin.validasi.nasabah.tolak', ['anggota' => '__ID__']) }}"
     data-setujui-url-template="{{ route('admin.validasi.nasabah.setujui', ['anggota' => '__ID__']) }}">
  <div class="relative bg-white w-full max-w-3xl mx-auto rounded-lg shadow-xl">
    <div class="flex justify-between items-center p-5 border-b rounded-t-lg">
      <h3 class="text-xl font-bold text-gray-900">Detail Informasi Nasabah</h3>
      <button onclick="closeInfoModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
      </button>
    </div>
    
    <div class="p-6 space-y-4">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Nama Lengkap</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-nama"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Nomor KTP</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-no_ktp"></dd>
            </div>
            <div class="col-span-2">
                <dt class="font-medium text-gray-500">Alamat Lengkap</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-alamat"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Nomor Telepon</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-nomor_telepon"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Pekerjaan</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-pekerjaan"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Pendapatan Bulanan</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-pendapatan"></dd>
            </div>
            <div class="col-span-1">
                <dt class="font-medium text-gray-500">Tanggal Diajukan</dt>
                <dd class="mt-1 font-semibold text-gray-900" id="modal-tanggal"></dd>
            </div>
        </dl>
    </div>
    
    <div class="flex items-center p-6 space-x-3 border-t border-gray-200 rounded-b-lg justify-end">
        <button onclick="closeInfoModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium">
            Tutup
        </button>
        
        <form id="form-tolak" method="POST" action="" class="inline-block">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                Tolak Pengajuan
            </button>
        </form>

        <form id="form-setujui" method="POST" action="" class="inline-block">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                Setujui Pengajuan
            </button>
        </form>
    </div>
  </div>
</div>
{{-- ======================================================= --}}

{{-- ======================================================= --}}
{{-- JAVASCRIPT UNTUK MODAL (DENGAN UPDATE) --}}
{{-- ======================================================= --}}
<script>
    const infoModal = document.getElementById('infoModal');
    
    // Ambil elemen dd (description details)
    const modalNama = document.getElementById('modal-nama');
    const modalNoKtp = document.getElementById('modal-no_ktp');
    const modalAlamat = document.getElementById('modal-alamat');
    const modalNomorTelepon = document.getElementById('modal-nomor_telepon');
    const modalPekerjaan = document.getElementById('modal-pekerjaan');
    const modalPendapatan = document.getElementById('modal-pendapatan');
    const modalTanggal = document.getElementById('modal-tanggal');
    
    // Ambil template URL dari data-attribute
    const tolakUrlTemplate = infoModal.dataset.tolakUrlTemplate;
    const setujuiUrlTemplate = infoModal.dataset.setujuiUrlTemplate;
    const tolakForm = document.getElementById('form-tolak');
    const setujuiForm = document.getElementById('form-setujui');

    function showInfoModal(anggotaData) {
        // Mengisi info detail
        modalNama.textContent = anggotaData.nama || '-';
        modalNoKtp.textContent = anggotaData.no_ktp || '-';
        modalAlamat.textContent = anggotaData.alamat || '-';
        modalNomorTelepon.textContent = anggotaData.nomor_telepon || '-';
        modalPekerjaan.textContent = anggotaData.pekerjaan || '-';
        modalPendapatan.textContent = anggotaData.pendapatan_bulanan ? 
            'Rp ' + parseInt(anggotaData.pendapatan_bulanan).toLocaleString('id-ID') : '-';
        modalTanggal.textContent = anggotaData.created_at ? 
            new Date(anggotaData.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';

        // Set URL action formulir secara dinamis
        tolakForm.action = tolakUrlTemplate.replace('__ID__', anggotaData.id);
        setujuiForm.action = setujuiUrlTemplate.replace('__ID__', anggotaData.id);

        infoModal.classList.remove('hidden'); // Tampilkan modal
    }

    function closeInfoModal() {
        infoModal.classList.add('hidden'); // Sembunyikan modal
    }
</script>