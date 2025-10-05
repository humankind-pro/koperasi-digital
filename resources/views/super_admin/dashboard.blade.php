<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <nav class="bg-white shadow-sm rounded-lg p-4 mt-6">
                <div class="flex space-x-4">
                    {{-- Ganti '#' dengan route yang sesuai nanti --}}
                    <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Rekapan Data
                    </a>
                    <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Data Karyawan
                    </a>
                    <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Data Admin
                    </a>
                </div>
            </nav>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Total Admin</h3>
                    <p class="text-3xl font-bold mt-2">5</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Total Karyawan</h3>
                    <p class="text-3xl font-bold mt-2">24</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Pinjaman Bulan Ini</h3>
                    <p class="text-3xl font-bold mt-2">12</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>