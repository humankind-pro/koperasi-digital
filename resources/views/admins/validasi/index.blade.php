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
                                    {{-- ======================================================= --}}
                                    {{-- HANYA TERSISA TOMBOL INFO & TINDAKAN --}}
                                    {{-- ======================================================= --}}
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
{{-- MODAL INFO (SUDAH TERMASUK TOMBOL AKSI) --}}
{{-- ======================================================= --}}
{{-- Kita tambahkan data-template-url untuk JavaScript --}}
<div id="infoModal" 
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50"
     data-tolak-url-template="{{ route('admin.validasi.nasabah.tolak', ['anggota' => '__ID__']) }}"
     data-setujui-url-template="{{ route('admin.validasi.nasabah.setujui', ['anggota' => '__ID__']) }}">
  <div class="relative p-8 bg-white w-full max-w-2xl mx-auto rounded-lg shadow-xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-xl font-bold text-gray-900">Detail Informasi Nasabah</h3>
      <button onclick="closeInfoModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
    </div>
    <div class="mt-2 text-sm text-gray-700 space-y-2">
      <p><strong>Nama:</strong> <span id="modal-nama"></span></p>
      <p><strong>No KTP:</strong> <span id="modal-no_ktp"></span></p>
      <p><strong>Alamat:</strong> <span id="modal-alamat"></span></p>
      <p><strong>No Telepon:</strong> <span id="modal-nomor_telepon"></span></p>
      <p><strong>Pekerjaan:</strong> <span id="modal-pekerjaan"></span></p>
      <p><strong>Pendapatan Bulanan:</strong> Rp <span id="modal-pendapatan"></span></p>
      <p><strong>Tanggal Diajukan:</strong> <span id="modal-tanggal"></span></p>
    </div>
    
    {{-- ======================================================= --}}
    {{-- TOMBOL AKSI DIPINDAHKAN KE SINI --}}
    {{-- ======================================================= --}}
    <div class="mt-6 pt-4 border-t flex justify-between items-center">
      <button onclick="closeInfoModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
        Tutup
      </button>
      <div class="space-x-2">
          {{-- Form Tolak --}}
          <form id="form-tolak" method="POST" action="" class="inline-block">
              @csrf
              @method('PATCH')
              <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600">
                  Tolak
              </button>
          </form>

          {{-- Form Setujui --}}
          <form id="form-setujui" method="POST" action="" class="inline-block">
              @csrf
              @method('PATCH')
              <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600">
                  Setujui
              </button>
          </form>
      </div>
    </div>
  </div>
</div>
{{-- ======================================================= --}}

{{-- ======================================================= --}}
{{-- JAVASCRIPT UNTUK MODAL (DENGAN UPDATE) --}}
{{-- ======================================================= --}}
<script>
    const infoModal = document.getElementById('infoModal');
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
            parseInt(anggotaData.pendapatan_bulanan).toLocaleString('id-ID') : '-';
        modalTanggal.textContent = anggotaData.created_at ? 
            new Date(anggotaData.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';

        // =======================================================
        // BARU: Set URL action formulir secara dinamis
        // =======================================================
        tolakForm.action = tolakUrlTemplate.replace('__ID__', anggotaData.id);
        setujuiForm.action = setujuiUrlTemplate.replace('__ID__', anggotaData.id);

        infoModal.classList.remove('hidden'); // Tampilkan modal
    }

    function closeInfoModal() {
        infoModal.classList.add('hidden'); // Sembunyikan modal
    }
</script>