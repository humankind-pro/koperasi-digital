<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Cari Riwayat Pinjaman</h3>
                    <p class="text-gray-500 mb-8">Masukkan NIK atau Nama Nasabah untuk melihat riwayat lengkap.</p>

                    {{-- 
                        SOLUSI: 
                        Form ini langsung mengirim data ke Controller Pinjaman (karyawan.pinjaman.riwayat).
                        Tidak pakai AJAX, tidak pakai route api yang error.
                    --}}
                    <form action="{{ route('karyawan.pinjaman.riwayat') }}" method="GET" class="space-y-4">
                        
                        <div>
                            <label for="search" class="sr-only">Cari NIK/Nama</label>
                            <input type="text" name="search" id="search" required
                                class="w-full text-center border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg py-3" 
                                placeholder="Ketik NIK atau Nama disini...">
                        </div>

                        <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition duration-150 transform hover:scale-105">
                            🔍 Cari Data Sekarang
                        </button>

                    </form>

                    <div class="mt-6 border-t pt-4">
                        <a href="{{ route('karyawan.pinjaman.riwayat') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                            Atau lihat semua riwayat terbaru &rarr;
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>