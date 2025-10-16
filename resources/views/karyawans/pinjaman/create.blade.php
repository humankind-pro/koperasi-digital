<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Formulir Pengajuan Pinjaman</h3>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pinjaman.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <x-input-label for="anggota_id" value="Pilih Nasabah (Hanya yang Disetujui)" />
                                <select name="anggota_id" id="anggota_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Nasabah --</option>
                                    @foreach ($anggotas as $anggota)
                                        <option value="{{ $anggota->id }}">{{ $anggota->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="jumlah_pinjaman" value="Jumlah Pinjaman (Rp)" />
                                <x-text-input id="jumlah_pinjaman" class="block mt-1 w-full" type="number" name="jumlah_pinjaman" :value="old('jumlah_pinjaman')" required />
                            </div>
                            <div>
                                <x-input-label for="tenor_bulan" value="Tenor (Bulan)" />
                                <x-text-input id="tenor_bulan" class="block mt-1 w-full" type="number" name="tenor_bulan" :value="old('tenor_bulan')" required />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="tujuan" value="Tujuan Pinjaman" />
                                <textarea id="tujuan" name="tujuan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('tujuan') }}</textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="px-4 py-2 bg-cyan-500 border rounded-md font-semibold text-xs text-white uppercase hover:bg-cyan-600">
                                Kirim Pengajuan Pinjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>