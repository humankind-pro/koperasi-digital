<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 px-6 lg:px-0">Daftar Pinjaman Aktif</h3>

            {{-- Menampilkan pesan sukses atau error --}}
            @if (session('success'))
                <div class="mb-4 mx-6 lg:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
             @if ($errors->any())
                <div class="mb-4 mx-6 lg:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-6 lg:px-0">
                @forelse ($pinjamanAktif as $pinjaman)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex flex-col justify-between">
                        <div>
                            <p class="font-bold text-lg text-gray-800">{{ $pinjaman->anggota->nama ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">Total Pinjaman: <span class="font-semibold">Rp {{ number_format($pinjaman->jumlah_disetujui, 0, ',', '.') }}</span></p>

                            {{-- LOGIKA SISA HUTANG ATAU LUNAS --}}
                            @if ($pinjaman->sisa_hutang <= 0)
                                <span class="mt-1 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Lunas 🎉
                                </span>
                            @else
                                <p class="mt-1 text-sm text-red-600">Sisa Hutang: <span class="font-semibold">Rp {{ number_format($pinjaman->sisa_hutang, 0, ',', '.') }}</span></p>
                            @endif
                            
                            {{-- =============================================== --}}
                            {{-- KODE YANG DIMASUKKAN (TENGGAT BERIKUTNYA) --}}
                            {{-- =============================================== --}}
                            @if ($pinjaman->sisa_hutang > 0 && $pinjaman->tenggat_berikutnya)
                                <p class="mt-1 text-sm text-gray-700">Tenggat Berikutnya: 
                                    <span class="font-semibold text-red-500">
                                        {{ \Carbon\Carbon::parse($pinjaman->tenggat_berikutnya)->format('d M Y') }}
                                    </span>
                                </p>
                            @endif
                            {{-- =============================================== --}}

                            <p class="mt-2 text-xs text-gray-500">Tenor: {{ $pinjaman->tenor_bulan }} bulan</p>
                            <p class="text-xs text-gray-500">Disetujui: {{ \Carbon\Carbon::parse($pinjaman->tanggal_validasi)->format('d M Y') }}</p>
                        </div>
                        <div class="mt-4 text-right">
                            {{-- Tampilkan tombol hanya jika belum lunas --}}
                            @if ($pinjaman->sisa_hutang > 0)
                                <button type="button"
                                    class="px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded hover:bg-blue-600"
                                    onclick="openPaymentModal({{ $pinjaman->id }}, '{{ $pinjaman->anggota->nama }}', {{ $pinjaman->sisa_hutang }})">
                                    Catat Pembayaran
                                </button>
                            @else
                                <span class="text-sm text-gray-500 italic">Sudah Lunas</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 lg:col-span-3 text-center py-8 text-gray-500">
                        Belum ada pinjaman yang aktif.
                    </div>
                @endforelse
            </div>

            {{-- Pagination links --}}
             <div class="mt-6 px-6 lg:px-0">
                 {{ $pinjamanAktif->links() }}
             </div>

        </div>
    </div>
</x-app-layout>

{{-- =============================================== --}}
{{-- MODAL PEMBAYARAN --}}
{{-- =============================================== --}}
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50">
  <div class="relative p-8 bg-white w-full max-w-lg mx-auto rounded-lg shadow-xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-xl font-bold text-gray-900">Catat Pembayaran untuk <span id="modal-anggota-nama"></span></h3>
      <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
    </div>
    <form id="paymentForm" method="POST" action="{{ route('karyawan.pembayaran.store') }}">
        @csrf
        <input type="hidden" name="pinjaman_id" id="modal-pinjaman-id">
        <div class="mt-2 text-sm text-gray-700 space-y-4">
            <p>Sisa Hutang Saat Ini: <strong class="text-red-600">Rp <span id="modal-sisa-hutang"></span></strong></p>
            <div>
                <label for="jumlah_bayar" class="block font-medium text-gray-700">Jumlah Bayar (Rp)</label>
                <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required min="1">
            </div>
            <div>
                <label for="tanggal_bayar" class="block font-medium text-gray-700">Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required value="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div class="mt-6 text-right">
             <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-2">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                Simpan Pembayaran
            </button>
        </div>
    </form>
  </div>
</div>

{{-- =============================================== --}}
{{-- JAVASCRIPT UNTUK MODAL --}}
{{-- =============================================== --}}
<script>
    const paymentModal = document.getElementById('paymentModal');
    const modalPinjamanId = document.getElementById('modal-pinjaman-id');
    const modalAnggotaNama = document.getElementById('modal-anggota-nama');
    const modalSisaHutang = document.getElementById('modal-sisa-hutang');
    const inputJumlahBayar = document.getElementById('jumlah_bayar');

    function openPaymentModal(pinjamanId, anggotaNama, sisaHutang) {
        modalPinjamanId.value = pinjamanId;
        modalAnggotaNama.textContent = anggotaNama;
        modalSisaHutang.textContent = parseInt(sisaHutang).toLocaleString('id-ID');
        inputJumlahBayar.max = sisaHutang; // Set max value for input
        inputJumlahBayar.value = ''; // Reset input value
        paymentModal.classList.remove('hidden');
    }

    function closePaymentModal() {
        paymentModal.classList.add('hidden');
    }
</script>