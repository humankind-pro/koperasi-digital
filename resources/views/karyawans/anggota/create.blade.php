<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        Formulir Pengajuan Nasabah Baru
                    </h3>

                    {{-- Menampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('anggota.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nama" value="Nama Lengkap" />
                                <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus />
                            </div>

                            <div>
                                <x-input-label for="no_ktp" value="Nomor KTP" />
                                <x-text-input id="no_ktp" class="block mt-1 w-full" type="text" name="no_ktp" :value="old('no_ktp')" required />
                            </div>

                            <div>
                                <x-input-label for="pekerjaan" value="Pekerjaan" />
                                <x-text-input id="pekerjaan" class="block mt-1 w-full" type="text" name="pekerjaan" :value="old('pekerjaan')" required />
                            </div>

                            <div>
                                <x-input-label for="pendapatan_bulanan" value="Pendapatan Bulanan (Rp)" />
                                <x-text-input id="pendapatan_bulanan" class="block mt-1 w-full" type="number" name="pendapatan_bulanan" :value="old('pendapatan_bulanan')" required />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="nomor_telepon" value="Nomor Telepon" />
                                <x-text-input id="nomor_telepon" class="block mt-1 w-full" type="text" name="nomor_telepon" :value="old('nomor_telepon')" required />
                            </div>
                            
                            <div class="md:col-span-2">
                                <x-input-label for="alamat" value="Alamat Lengkap" />
                                <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm" rows="3">{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('anggota.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-cyan-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>