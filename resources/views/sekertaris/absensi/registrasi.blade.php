<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- HEADER HALAMAN --}}
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Monitor & Registrasi Fingerprint</h2>
                <span class="bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full font-bold animate-pulse">
                    🟢 Live Connection
                </span>
            </div>

            {{-- ========================================================= --}}
            {{-- FITUR 1: AUTO DETECT (KOTAK KUNING MUNCUL OTOMATIS)       --}}
            {{-- ========================================================= --}}
            @if($idAsing)
            <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-400 p-6 shadow-lg rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        {{-- Ikon Fingerprint --}}
                        <svg class="h-12 w-12 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.2-2.858.571-4.183m4.823-3.235A13.926 13.926 0 0112 3c1.789 0 3.512.446 5.093 1.259" />
                        </svg>
                    </div>
                    <div class="ml-4 w-full">
                        <h3 class="text-lg font-bold text-gray-900">
                            Terdeteksi Jari Baru: <span class="text-yellow-700 text-2xl">ID #{{ $idAsing->fingerprint_id }}</span>
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Data masuk pada: {{ $idAsing->created_at->format('H:i:s') }}. 
                            Silakan pilih karyawan pemilik jari ini untuk menghubungkannya.
                        </p>
                        
                        {{-- FORM MENGHUBUNGKAN --}}
                        <form action="{{ route('sekertaris.absensi.hubungkan') }}" method="POST" class="mt-4 flex gap-3 items-center">
                            @csrf
                            <input type="hidden" name="fingerprint_id" value="{{ $idAsing->fingerprint_id }}">
                            
                            <div class="flex-grow max-w-md">
                                <select name="user_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Pilih Pegawai --</option>
                                    {{-- Hanya tampilkan user yang BELUM punya ID --}}
                                    @foreach(\App\Models\User::whereNull('fingerprint_id')->orWhere('fingerprint_id', 0)->orderBy('name')->get() as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow font-bold text-sm transition transform hover:scale-105">
                                🔗 Hubungkan Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- PESAN SUKSES / ERROR --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ========================================================= --}}
            {{-- FITUR 2: LOG LIVE (MONITOR AKTIVITAS MESIN)               --}}
            {{-- ========================================================= --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                        <span>📋</span> Log Aktivitas Mesin (Realtime)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ID Alat</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Karyawan</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $log->created_at->format('H:i:s') }}
                                        <span class="text-xs text-gray-400 block">{{ $log->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-600 font-bold">
                                        #{{ $log->fingerprint_id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->user)
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-2">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ $log->user->name }}</span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Belum Terdaftar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($log->action == 'verification_success')
                                            <span class="text-green-600 font-bold flex items-center gap-1">
                                                ✅ Absen Masuk
                                            </span>
                                        @elseif($log->action == 'enroll')
                                            <span class="text-blue-600 font-bold flex items-center gap-1">
                                                📝 Pendaftaran Jari
                                            </span>
                                        @elseif($log->action == 'verification_failed')
                                            <span class="text-red-500 flex items-center gap-1">
                                                ❌ Gagal / Tidak Dikenal
                                            </span>
                                        @else
                                            <span class="text-gray-600">{{ $log->action }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Script Auto Refresh (Opsional: Agar log update sendiri tiap 5 detik) --}}
    <script>
        setTimeout(function(){
           window.location.reload(1);
        }, 10000); // Refresh halaman setiap 10 detik
    </script>
</x-app-layout>