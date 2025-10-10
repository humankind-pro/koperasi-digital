<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-cyan-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold opacity-80">Jumlah Karyawan</h3>
                    <p class="text-4xl font-bold mt-2">
                        {{-- Ganti dengan data dinamis nanti, contoh: \App\Models\User::where('role', 'karyawan')->count() --}}
                        24
                    </p>
                </div>

                <div class="bg-cyan-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold opacity-80">Jumlah Admin</h3>
                    <p class="text-4xl font-bold mt-2">
                        {{-- Ganti dengan data dinamis nanti, contoh: \App\Models\User::where('role', 'admin')->count() --}}
                        5
                    </p>
                </div>

                <div class="bg-cyan-600 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold opacity-80">Pinjaman Bulan Ini</h3>
                    <p class="text-4xl font-bold mt-2">
                        {{-- Ganti dengan data dinamis nanti --}}
                        12
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>