<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Services\MLDataScrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::where('dibuat_oleh_user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('karyawans.anggota.index', compact('anggotas'));
    }

    public function create()
    {
        return view('karyawans.anggota.create');
    }

    public function store(Request $request)
    {
        Log::info('========================================');
        Log::info('📝 FORM SUBMITTED WITH COMPLETE DATA!');
        Log::info('========================================');
        Log::info('Request Data:', $request->except(['foto_ktp', 'foto_selfie_ktp']));

        try {
            // ========================================
            // VALIDATION RULES - EXACT MATCH DENGAN FORM
            // ========================================
            $validated = $request->validate([
                // Data KTP
                'nik'                  => 'required|string|size:16|unique:anggota,nik',
                'nama'                 => 'required|string|max:150',
                'tempat_lahir'         => 'nullable|string|max:100',
                'tanggal_lahir'        => 'nullable|string|max:20',
                'jenis_kelamin'        => 'required|in:L,P',
                'agama'                => 'nullable|string|max:50',
                'status_perkawinan'    => 'nullable|in:BELUM KAWIN,KAWIN,CERAI HIDUP,CERAI MATI',
                'pekerjaan'            => 'nullable|string|max:100',
                'alamat'               => 'nullable|string',
                'rt'                   => 'nullable|string|max:10',
                'rw'                   => 'nullable|string|max:10',
                'kelurahan'            => 'nullable|string|max:100',
                'kecamatan'            => 'nullable|string|max:100',
                'kabupaten_kota'       => 'nullable|string|max:100',
                'provinsi'             => 'nullable|string|max:100',
                'gol_darah'            => 'nullable|string|max:5',

                // Data Tambahan & Finansial
                'nomor_telepon'        => 'required|numeric|digits_between:10,15',
                'pendidikan'           => 'required|string|in:SD,SMP,SMA/SMK,D3,S1,S2,S3',
                'jumlah_tanggungan'    => 'required|integer|min:0|max:20',
                'umur'                 => 'required|integer|min:17|max:100',
                
                // ✅ NAMA EXACT DARI FORM
                'Lama_Bekerja_Tahun'   => 'required|numeric|min:0|max:50',
                'status_tempat_tinggal'=> 'required|string|in:Milik Sendiri,Sewa,Kontrak,Milik Orang Tua',
                'pendapatan_bulanan'   => 'required|numeric|min:0',
                'pengeluaran_bulanan'  => 'required|numeric|min:0',

                // Data Pinjaman
                'jumlah_pinjaman'      => 'required|numeric|min:100000|max:1000000000',
                
                // ✅ NAMA EXACT DARI FORM
                'Lama_Tenor_Bulan'     => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,18,24,36,48,60',
                'tujuan_pinjaman'      => 'required|string|in:Modal Usaha,Pendidikan,Renovasi,Kesehatan,Pernikahan,Konsumtif,Investasi,Lainya',
                'ada_jaminan'          => 'required|in:ya,tidak',
                'jaminan'              => 'nullable|string|max:500',
                'jaminan_lainnya'      => 'nullable|string|max:500',
                
                // ✅ VALIDATION UNTUK RIWAYAT TUNGGAKAN
                'riwayat_tunggakan'    => 'required|in:Tidak,Pernah,Sering',

                // File Upload
                'foto_ktp'             => 'required|image|mimes:jpg,jpeg,png|max:5120',
                'foto_selfie_ktp'      => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ], [
                // Custom Error Messages
                'nik.required' => 'NIK wajib diisi',
                'nik.size' => 'NIK harus 16 digit',
                'nik.unique' => 'NIK sudah terdaftar',
                'nama.required' => 'Nama lengkap wajib diisi',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
                'nomor_telepon.required' => 'Nomor WhatsApp wajib diisi',
                'nomor_telepon.digits_between' => 'Nomor WhatsApp harus 10-15 digit',
                'pendidikan.required' => 'Pendidikan wajib dipilih',
                'jumlah_tanggungan.required' => 'Jumlah tanggungan wajib diisi',
                'umur.required' => 'Umur wajib diisi',
                'umur.min' => 'Umur minimal 17 tahun',
                
                'Lama_Bekerja_Tahun.required' => 'Lama bekerja wajib diisi',
                'Lama_Bekerja_Tahun.numeric' => 'Lama bekerja harus berupa angka',
                'Lama_Bekerja_Tahun.min' => 'Lama bekerja minimal 0 tahun',
                'Lama_Bekerja_Tahun.max' => 'Lama bekerja maksimal 50 tahun',
                
                'status_tempat_tinggal.required' => 'Status tempat tinggal wajib dipilih',
                'pendapatan_bulanan.required' => 'Pendapatan bulanan wajib diisi',
                'pengeluaran_bulanan.required' => 'Pengeluaran bulanan wajib diisi',
                'jumlah_pinjaman.required' => 'Jumlah pinjaman wajib diisi',
                'jumlah_pinjaman.min' => 'Jumlah pinjaman minimal Rp 100.000',
                
                'Lama_Tenor_Bulan.required' => 'Tenor pinjaman wajib dipilih',
                'Lama_Tenor_Bulan.in' => 'Tenor pinjaman tidak valid',
                
                'tujuan_pinjaman.required' => 'Tujuan pinjaman wajib dipilih',
                'ada_jaminan.required' => 'Status jaminan wajib dipilih',
                
                'riwayat_tunggakan.required' => 'Riwayat tunggakan wajib diisi',
                'riwayat_tunggakan.in' => 'Riwayat tunggakan harus: Tidak, Pernah, atau Sering',
                
                'foto_ktp.required' => 'Foto KTP wajib diupload',
                'foto_ktp.image' => 'File KTP harus berupa gambar',
                'foto_ktp.mimes' => 'Format KTP harus JPG, JPEG, atau PNG',
                'foto_ktp.max' => 'Ukuran foto KTP maksimal 5MB',
                
                'foto_selfie_ktp.required' => 'Foto selfie + KTP wajib diupload',
                'foto_selfie_ktp.image' => 'File selfie harus berupa gambar',
                'foto_selfie_ktp.mimes' => 'Format selfie harus JPG, JPEG, atau PNG',
                'foto_selfie_ktp.max' => 'Ukuran foto selfie maksimal 5MB',
            ]);

            // ========================================
            // VALIDASI TAMBAHAN UNTUK JAMINAN
            // ========================================
            if ($validated['ada_jaminan'] === 'ya') {
                if (empty($validated['jaminan']) && empty($validated['jaminan_lainnya'])) {
                    return back()
                        ->withErrors(['jaminan' => 'Jaminan wajib diisi karena Anda memilih "Ada Jaminan"'])
                        ->withInput();
                }
                
                // Jika pilih "Lainnya" tapi tidak isi detail
                if ($validated['jaminan'] === 'Lainnya' && empty($validated['jaminan_lainnya'])) {
                    return back()
                        ->withErrors(['jaminan_lainnya' => 'Jelaskan jaminan lainnya yang Anda miliki'])
                        ->withInput();
                }
            }

            Log::info('✅ VALIDATION PASSED!');

            // ========================================
            // TRANSFORMASI DATA UNTUK DATABASE
            // ========================================
            
            // Format Tempat Tanggal Lahir
            $tempat_tanggal_lahir = $this->formatTempatTanggalLahir(
                $validated['tempat_lahir'] ?? '',
                $validated['tanggal_lahir'] ?? ''
            );

            // Format RT/RW
            $rt_rw = $this->formatRtRw(
                $validated['rt'] ?? '',
                $validated['rw'] ?? ''
            );

            // Kelurahan/Desa
            $kelurahan_desa = $validated['kelurahan'] ?? null;
            
            // Jenis Kelamin
            $jenis_kelamin = $validated['jenis_kelamin'];
            
            // Status Pernikahan - Normalize
            $status_pernikahan_asli = strtoupper(
                $this->cleanText($validated['status_perkawinan'] ?? '')
            ) ?: 'BELUM KAWIN';

            // Lama Bekerja - Convert ke float dan format text
            $lama_bekerja_tahun = (float) $validated['Lama_Bekerja_Tahun'];
            $lama_bekerja_text = $this->formatLamaBekerjaText($lama_bekerja_tahun);
            
            // Riwayat Tunggakan - Sudah validated, gunakan langsung
            $riwayat_tunggakan_asli = $validated['riwayat_tunggakan'];

            // ========================================
            // PROSES JAMINAN
            // ========================================
            $jaminan_final = null;
            $keterangan_jaminan = null;
            
            if ($validated['ada_jaminan'] === 'ya') {
                // Prioritas: jaminan_lainnya > jaminan
                if (!empty($validated['jaminan_lainnya'])) {
                    $jaminan_final = $validated['jaminan_lainnya'];
                    $keterangan_jaminan = 'Jaminan Lainnya';
                } elseif (!empty($validated['jaminan'])) {
                    $jaminan_final = $validated['jaminan'];
                    $keterangan_jaminan = null;
                } else {
                    // Fallback (seharusnya tidak terjadi karena sudah divalidasi)
                    $jaminan_final = 'Jaminan Umum';
                }
            } else {
                // Jika tidak ada jaminan
                $jaminan_final = null;
                $keterangan_jaminan = null;
            }

            Log::info('✅ DATA TRANSFORMATION COMPLETED', [
                'lama_bekerja_tahun' => $lama_bekerja_tahun,
                'lama_bekerja_text' => $lama_bekerja_text,
                'status_pernikahan' => $status_pernikahan_asli,
                'riwayat_tunggakan' => $riwayat_tunggakan_asli,
                'jaminan_final' => $jaminan_final,
                'tenor' => $validated['Lama_Tenor_Bulan'],
            ]);

            // ========================================
            // DATABASE TRANSACTION
            // ========================================
            DB::beginTransaction();

            try {
                // Generate Kode Anggota
                $lastAnggota = Anggota::lockForUpdate()->orderBy('id', 'desc')->first();
                $lastNumber = $lastAnggota ? (int) substr($lastAnggota->kode_anggota, -5) : 0;
                $kode = 'ANG' . date('Ym') . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

                Log::info('🔢 Generated kode anggota:', ['kode' => $kode]);

                // Upload Files
                $timestamp = now()->format('YmdHis');
                $nikSafe = substr($validated['nik'], 0, 8);

                $pathKtp = $request->file('foto_ktp')->storeAs(
                    'ktp',
                    "KTP_{$kode}{$nikSafe}{$timestamp}.jpg",
                    'public'
                );

                $pathSelfie = $request->file('foto_selfie_ktp')->storeAs(
                    'selfie',
                    "SELFIE_{$kode}{$nikSafe}{$timestamp}.jpg",
                    'public'
                );

                Log::info('📷 Files uploaded successfully', [
                    'ktp' => $pathKtp,
                    'selfie' => $pathSelfie
                ]);

                // ========================================
                // PREPARE DATA TO SAVE
                // ========================================
                $dataToSave = [
                    // Identitas
                    'kode_anggota'         => $kode,
                    'nama'                 => $this->cleanNama($validated['nama']),
                    'nik'                  => $validated['nik'],
                    'tempat_tanggal_lahir' => $tempat_tanggal_lahir,
                    'jenis_kelamin'        => $jenis_kelamin,
                    'agama'                => $this->cleanText($validated['agama'] ?? '') ?: null,
                    'status_perkawinan'    => $status_pernikahan_asli,
                    'pekerjaan'            => $this->cleanText($validated['pekerjaan'] ?? '') ?: null,
                    'kewarganegaraan'      => 'WNI',
                    'gol_darah'            => !empty($validated['gol_darah']) ? strtoupper(trim($validated['gol_darah'])) : null,
                    
                    // Alamat
                    'alamat'               => $this->cleanText($validated['alamat'] ?? ''),
                    'rt_rw'                => $rt_rw,
                    'kelurahan_desa'       => $this->cleanText($kelurahan_desa),
                    'kecamatan'            => $this->cleanText($validated['kecamatan'] ?? ''),
                    'kabupaten_kota'       => $this->cleanText($validated['kabupaten_kota'] ?? ''),
                    'provinsi'             => $this->cleanText($validated['provinsi'] ?? ''),
                    
                    // Kontak
                    'nomor_telepon'        => $validated['nomor_telepon'],
                    
                    // Data ML
                    'umur'                 => $validated['umur'],
                    'pendidikan'           => $validated['pendidikan'],
                    'lama_bekerja'         => $lama_bekerja_text,
                    'lama_bekerja_tahun'   => $lama_bekerja_tahun,
                    'jumlah_tanggungan'    => $validated['jumlah_tanggungan'],
                    'status_tempat_tinggal'=> $validated['status_tempat_tinggal'],
                    'pendapatan_bulanan'   => $validated['pendapatan_bulanan'],
                    'pengeluaran_bulanan'  => $validated['pengeluaran_bulanan'],
                    'skor_kredit'          => 50, // Default untuk nasabah baru
                    
                    // Data Pinjaman
                    'jumlah_pinjaman'      => $validated['jumlah_pinjaman'],
                    'tenor'                => $validated['Lama_Tenor_Bulan'], // ✅ Save ke kolom 'tenor'
                    'tujuan_pinjaman'      => $validated['tujuan_pinjaman'],
                    'jaminan'              => $jaminan_final,
                    'keterangan_jaminan'   => $keterangan_jaminan,
                    'riwayat_tunggakan'    => $riwayat_tunggakan_asli, // ✅ 'Tidak', 'Pernah', atau 'Sering'
                    
                    // Dokumen
                    'foto_ktp'             => $pathKtp,
                    'foto_selfie_ktp'      => $pathSelfie,
                    
                    // Status & Workflow
                    'status'               => 'pending',
                    'dibuat_oleh_user_id'  => Auth::id(),
                ];

                Log::info('💾 Saving data to database...', $dataToSave);

                // Save to Database
                $anggota = Anggota::create($dataToSave);

                Log::info('✅ DATA SAVED SUCCESSFULLY!', [
                    'id' => $anggota->id,
                    'kode' => $anggota->kode_anggota,
                ]);

                // ========================================
                // ML PREDICTION
                // ========================================
                Log::info('🤖 Starting ML prediction...');
                
                try {
                    $mlScrapper = new MLDataScrapper();

                    // Siapkan data untuk ML (MLDataScrapper akan mapping)
                    $mlInputData = $validated;

                    // Jalankan prediksi
                    $mlResult = $mlScrapper->predictEligibility($mlInputData);

                    if ($mlResult['success']) {
                        Log::info('✅ ML Prediction SUCCESS!', $mlResult['data']);

                        // Update kelayakan di database
                        $anggota->update([
                            'kelayakan' => $mlResult['data']['kelayakan'],
                            'tanggal_prediksi' => now(),
                        ]);

                        Log::info('✅ Kelayakan updated in database', [
                            'kelayakan' => $mlResult['data']['kelayakan'],
                            'confidence' => $mlResult['data']['confidence'] ?? 'N/A'
                        ]);
                    } else {
                        Log::warning('⚠ ML Prediction FAILED', [
                            'error' => $mlResult['error']
                        ]);
                        
                        // ML gagal tapi data tetap tersimpan
                        // Kelayakan akan null, bisa di-review manual oleh admin
                    }
                    
                } catch (\Exception $mlException) {
                    Log::error('❌ ML Scrapper Exception', [
                        'error' => $mlException->getMessage(),
                        'trace' => $mlException->getTraceAsString(),
                    ]);
                    
                    // ML error tidak menghentikan proses
                    // Data tetap tersimpan, kelayakan bisa di-review manual
                }

                // Commit transaction
                DB::commit();

                Log::info('✅ TRANSACTION COMMITTED SUCCESSFULLY!');

                // Build success message
                $message = $this->buildSuccessMessage($anggota, $validated, $lama_bekerja_tahun, $jaminan_final);

                return redirect()
                    ->route('anggota.index')
                    ->with('success', $message);

            } catch (Exception $e) {
                // Rollback transaction
                DB::rollBack();

                Log::error('❌ DATABASE TRANSACTION ERROR!', [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Delete uploaded files if transaction failed
                if (isset($pathKtp) && Storage::disk('public')->exists($pathKtp)) {
                    Storage::disk('public')->delete($pathKtp);
                    Log::info('🗑 Deleted uploaded KTP file');
                }
                if (isset($pathSelfie) && Storage::disk('public')->exists($pathSelfie)) {
                    Storage::disk('public')->delete($pathSelfie);
                    Log::info('🗑 Deleted uploaded Selfie file');
                }

                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ VALIDATION ERROR!', [
                'errors' => $e->errors()
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (Exception $e) {
            Log::error('❌ GENERAL ERROR!', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function searchByNik(Request $request)
    {
        $request->validate(['nik' => 'required|digits:16']);

        $anggota = Anggota::where('nik', $request->nik)
            ->where('dibuat_oleh_user_id', Auth::id())
            ->where('status', 'disetujui')
            ->first(['id', 'nama', 'kode_anggota']);

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Nasabah tidak ditemukan atau belum disetujui.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'anggota' => $anggota
        ]);
    }

    /**
     * API Pencarian Nasabah untuk Form Pengajuan Pinjaman
     * Method ini dipanggil oleh AJAX di create.blade.php
     */
    public function cariNasabahByNik(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        // Cari Anggota
        $anggota = Anggota::where('nik', $nik)
                          ->where('dibuat_oleh_user_id', Auth::id()) // Hanya nasabah buatan karyawan ini
                          ->first(); // Ambil SEMUA field (jangan cuma id & nama)

        if ($anggota) {
            // Cek Status
            if ($anggota->status !== 'disetujui') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Nasabah ditemukan, tetapi statusnya masih Pending/Ditolak.'
                ]);
            }

            return response()->json(['success' => true, 'anggota' => $anggota]);
        } else {
            return response()->json(['success' => false, 'message' => 'Nasabah tidak ditemukan di daftar Anda.'], 404);
        }
    }

    public function showSearchRiwayatForm()
    {
        return view('karyawans.anggota.search_riwayat');
    }

    public function searchNikRiwayat(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        $anggota = Anggota::where('nik', $nik)
                          ->where('dibuat_oleh_user_id', Auth::id())
                          ->first();

        if (!$anggota) {
            // Coba cari dengan field 'nik' (dari dokumen 1)
            $anggota = Anggota::where('nik', $nik)
                              ->where('dibuat_oleh_user_id', Auth::id())
                              ->first();
            
            if (!$anggota) {
                return response()->json(['success' => false, 'message' => 'Nasabah tidak ditemukan.'], 404);
            }
        }

        $riwayatPinjaman = Pinjaman::where('anggota_id', $anggota->id)
                                    ->with(['diajukanOleh:id,name', 'divalidasiOleh:id,name', 'pembayaran'])
                                    ->latest('tanggal_pengajuan')
                                    ->get();

        return response()->json([
            'success' => true,
            'anggota' => $anggota,
            'riwayat' => $riwayatPinjaman
        ]);
    }

    public function indexForSuperAdmin()
    {
        $anggotas = Anggota::with('dibuatOleh')
            ->latest()
            ->paginate(15);

        return view('super_admin.anggota.index', compact('anggotas'));
    }

    public function edit(Anggota $anggota)
    {
        return view('super_admin.anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $validatedData = $request->validate([
            'nama'               => 'required|string|max:150',
            'nik'                => 'required|digits:16|unique:anggota,nik,' . $anggota->id,
            'nik'             => 'nullable|string|max:20|unique:anggota,nik,' . $anggota->id,
            'alamat'             => 'required|string',
            'nomor_telepon'      => 'required|digits_between:10,15',
            'pekerjaan'          => 'nullable|string|max:100',
            'pendapatan_bulanan' => 'required|numeric|min:0',
            'status'             => 'required|in:pending,disetujui,ditolak,aktif',
        ]);

        $anggota->update($validatedData);

        return redirect()
            ->route('superadmin.anggota.index')
            ->with('success', 'Data nasabah berhasil diperbarui.');
    }

    public function destroy(Anggota $anggota)
    {
        try {
            // Hapus file foto jika ada
            foreach (['foto_ktp', 'foto_selfie_ktp'] as $field) {
                if ($anggota->$field && Storage::disk('public')->exists($anggota->$field)) {
                    Storage::disk('public')->delete($anggota->$field);
                }
            }

            $anggota->delete();
            
            return redirect()
                ->route('superadmin.anggota.index')
                ->with('success', 'Nasabah berhasil dihapus.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()
                ->route('superadmin.anggota.index')
                ->with('error', 'Gagal menghapus anggota. Pastikan tidak ada data pinjaman terkait.');
        }
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Format lama bekerja dalam tahun menjadi text "X tahun Y bulan"
     */
    private function formatLamaBekerjaText($tahun)
    {
        $tahunInt = floor($tahun);
        $bulan = round(($tahun - $tahunInt) * 12);

        if ($tahunInt == 0 && $bulan == 0) {
            return '0 bulan';
        }

        $parts = [];
        if ($tahunInt > 0) {
            $parts[] = $tahunInt . ' tahun';
        }
        if ($bulan > 0) {
            $parts[] = $bulan . ' bulan';
        }

        return implode(' ', $parts);
    }

    /**
     * Clean dan format nama (Title Case)
     */
    private function cleanNama($text)
    {
        $text = trim($text);
        if (empty($text)) return 'Tidak terbaca';

        // Remove special characters except spaces, hyphens, dots
        $text = preg_replace('/[^a-zA-Z\s\-\.]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return Str::title(strtolower($text));
    }

    /**
     * Clean text - remove control characters
     */
    private function cleanText($text)
    {
        if (is_null($text)) return null;

        $text = trim($text);
        if (empty($text)) return null;

        // Remove control characters
        $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return !empty($text) ? $text : null;
    }

    /**
     * Format tempat tanggal lahir
     */
    private function formatTempatTanggalLahir($tempat, $tanggal)
    {
        $tempat = trim($tempat);
        $tanggal = trim($tanggal);

        if (empty($tempat) && empty($tanggal)) {
            return 'Tidak terbaca';
        }

        if (empty($tempat)) return $tanggal;
        if (empty($tanggal)) return strtoupper($tempat);

        return strtoupper($tempat) . ' / ' . $tanggal;
    }

    /**
     * Format RT/RW dengan padding
     */
    private function formatRtRw($rt, $rw)
    {
        $rt = trim($rt);
        $rw = trim($rw);

        if (empty($rt) && empty($rw)) return null;

        // Extract only numbers
        $rt = preg_replace('/\D/', '', $rt);
        $rw = preg_replace('/\D/', '', $rw);

        // Pad with zeros
        $rtFormatted = str_pad($rt ?: '000', 3, '0', STR_PAD_LEFT);
        $rwFormatted = str_pad($rw ?: '000', 3, '0', STR_PAD_LEFT);

        return $rtFormatted . '/' . $rwFormatted;
    }

    /**
     * Build success message dengan HTML formatting
     */
    private function buildSuccessMessage($anggota, $validated, $lamaBekerja, $jaminan)
    {
        $message = "✅ <strong>Pengajuan {$anggota->kode_anggota} Berhasil Disimpan!</strong><br><br>";

        $message .= "📊 <strong>Data Pengajuan:</strong><br>";
        $message .= "👤 Nama: <strong>{$anggota->nama}</strong><br>";
        $message .= "📱 WhatsApp: <strong>{$validated['nomor_telepon']}</strong><br>";
        $message .= "🎓 Pendidikan: <strong>{$validated['pendidikan']}</strong><br>";
        $message .= "👶 Tanggungan: <strong>{$validated['jumlah_tanggungan']} orang</strong><br>";
        $message .= "👨‍💼 Lama Bekerja: <strong>{$anggota->lama_bekerja}</strong> ({$lamaBekerja} tahun)<br>";
        $message .= "🏠 Tempat Tinggal: <strong>{$validated['status_tempat_tinggal']}</strong><br><br>";

        $message .= "💰 <strong>Keuangan:</strong><br>";
        $message .= "💵 Pendapatan: <strong>Rp " . number_format($validated['pendapatan_bulanan'], 0, ',', '.') . "</strong><br>";
        $message .= "💸 Pengeluaran: <strong>Rp " . number_format($validated['pengeluaran_bulanan'], 0, ',', '.') . "</strong><br>";
        $message .= "💎 Skor Kredit: <strong>50</strong> (Default untuk nasabah baru)<br><br>";

        $message .= "🏦 <strong>Detail Pinjaman:</strong><br>";
        $message .= "💵 Jumlah: <strong>Rp " . number_format($validated['jumlah_pinjaman'], 0, ',', '.') . "</strong><br>";
        $message .= "📅 Tenor: <strong>{$validated['Lama_Tenor_Bulan']} Bulan</strong><br>";
        $message .= "🎯 Tujuan: <strong>{$validated['tujuan_pinjaman']}</strong><br>";
        $message .= "🔐 Jaminan: <strong>" . ($jaminan ?: 'Tidak Ada') . "</strong>";

        if ($anggota->keterangan_jaminan) {
            $message .= " ({$anggota->keterangan_jaminan})";
        }
        $message .= "<br><br>";

        if ($anggota->kelayakan) {
            $statusIcon = $anggota->kelayakan === 'Layak' ? '✅' : 
                         ($anggota->kelayakan === 'Dipertimbangkan' ? '⚠' : '❌');
            $message .= "🤖 <strong>Hasil Analisis AI:</strong> {$statusIcon} {$anggota->kelayakan}<br><br>";
        }

        $message .= "⏳ <strong>Status:</strong> Pending - Menunggu persetujuan admin.";

        return $message;
    }
}