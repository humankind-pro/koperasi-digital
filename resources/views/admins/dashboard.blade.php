<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('admin.validasi.index') }}" class="block p-6 bg-cyan-500 text-white rounded-lg shadow-lg hover:bg-cyan-600 transition transform hover:-translate-y-1">
                    <h5 class="text-3xl font-bold tracking-tight">Validasi Pengajuan Nasabah</h5>
                    <p class="font-normal text-cyan-100 mt-1">Lihat dan proses semua pengajuan nasabah yang menunggu persetujuan.</p>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Pengajuan Menunggu Validasi</h3>
                    <p class="text-4xl font-bold mt-2 text-amber-500">
                        {{ $pendingCount }}
                    </p>
                </div>

                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Disetujui Hari Ini</h3>
                    <p class="text-4xl font-bold mt-2 text-green-500">
                        {{ $approvedTodayCount }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>