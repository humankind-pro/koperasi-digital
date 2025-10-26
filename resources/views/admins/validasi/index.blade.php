<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Validasi Pengajuan Anggota Baru
                    </h3>

                    {{-- Menampilkan pesan sukses --}}
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
                                    {{-- Tombol Info --}}
                                    <button
                                        type="button"
                                        class="px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded hover:bg-blue-600"
                                        onclick="showInfoModal({{ json_encode($anggota) }})">
                                        Info
                                    </button>

                                    {{-- Form Tolak --}}
                                    <form action="{{ route('admin.validasi.nasabah.tolak', $anggota->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-red-500 rounded hover:bg-red-600">
                                            Tolak
                                        </button>
                                    </form>

                                    {{-- Form Setujui --}}
                                    <form action="{{ route('admin.validasi.nasabah.setujui', $anggota->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-green-500 rounded hover:bg-green-600">
                                            Setujui
                                        </button>
                                    </form>
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
{{-- STRUKTUR MODAL --}}
{{-- ======================================================= --}}
<div id="infoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50">
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
    <div class="mt-6 text-right">
      <button onclick="closeInfoModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
        Tutup
      </button>
    </div>
  </div>
</div>
{{-- ======================================================= --}}

{{-- ======================================================= --}}
{{-- JAVASCRIPT UNTUK MODAL --}}
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

    function showInfoModal(anggotaData) {
        modalNama.textContent = anggotaData.nama || '-';
        modalNoKtp.textContent = anggotaData.no_ktp || '-';
        modalAlamat.textContent = anggotaData.alamat || '-';
        modalNomorTelepon.textContent = anggotaData.nomor_telepon || '-';
        modalPekerjaan.textContent = anggotaData.pekerjaan || '-';
        // Format pendapatan ke format Rupiah
        modalPendapatan.textContent = anggotaData.pendapatan_bulanan ?
            parseInt(anggotaData.pendapatan_bulanan).toLocaleString('id-ID') : '-';
        // Format tanggal (menggunakan created_at karena tanggal_bergabung belum tentu ada saat pending)
        modalTanggal.textContent = anggotaData.created_at ?
            new Date(anggotaData.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';

        infoModal.classList.remove('hidden'); // Tampilkan modal
    }

    function closeInfoModal() {
        infoModal.classList.add('hidden'); // Sembunyikan modal
    }
</script>