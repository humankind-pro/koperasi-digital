<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Sekertaris</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-pink-500">
                    <p class="text-sm font-medium text-gray-500">Total Pengajuan Bulan Ini</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPinjamanBulanIni }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
                    <p class="text-sm font-medium text-gray-500">Total Dana Cair Bulan Ini</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalNominalDisetujui, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('sekertaris.pinjaman.rekap') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Buka Rekap Pinjaman Bulanan &rarr;
                </a>
            </div>
        </div>
    </div>
</x-app-layout>