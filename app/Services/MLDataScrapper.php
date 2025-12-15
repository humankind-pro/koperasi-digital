<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process; 
use Exception;

class MLDataScrapper
{
    protected $pythonPath;
    protected $pythonScriptPath;
    protected $modelPath;
    protected $timeout;

    public function __construct()
    {
        $this->pythonPath = env('PYTHON_PATH', 'python'); 
        $this->pythonScriptPath = env('PYTHON_SCRIPT_PATH', base_path('Sc/predict_script.py'));
        $this->modelPath = env('ML_MODEL_PATH', base_path('Sc/credit_model_ultra.pkl'));
        
        $this->timeout = env('ML_SCRIPT_TIMEOUT', 120);
    }

    public function predictEligibility(array $inputData)
    {
        try {
            Log::info('🤖 MLDataScrapper: Received raw input data for prediction', $inputData);
            $mappedData = $this->mapInputToPythonModelFormat($inputData);

            Log::info('🤖 MLDataScrapper: Mapped data for Python ML model', $mappedData);

            $scriptResponse = $this->runPythonScript($mappedData);

            if ($scriptResponse['success']) {
                return [
                    'success' => true,
                    'data' => [
                        'kelayakan' => $scriptResponse['prediction'],
                        'confidence' => $scriptResponse['confidence'] ?? null,
                        'mapped_input_used' => $mappedData
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $scriptResponse['error'] ?? 'Unknown error from Python script execution'
                ];
            }

        } catch (Exception $e) {
            Log::error('🤖 MLDataScrapper: Error during prediction process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Internal error during ML prediction: ' . $e->getMessage()
            ];
        }
    }
    private function mapInputToPythonModelFormat(array $inputData): array
    {
        $mapStatusPernikahan = function ($status) {
            if (empty($status)) {
                return 'Lajang'; 
            }

            $status = strtoupper(trim($status));

            if (in_array($status, ['LAJANG', 'BELUM KAWIN', 'BELUM MENIKAH', 'SINGLE'])) {
                return 'Lajang';
            }

            if (in_array($status, ['MENIKAH', 'KAWIN', 'MARRIED'])) {
                return 'Menikah';
            }

            if (in_array($status, ['CERAI', 'CERAI HIDUP', 'CERAI MATI', 'DUDA', 'JANDA'])) {
                return 'Cerai';
            }

            return 'Lajang'; 
        };

        $mapPekerjaan = function ($pekerjaan) {
            if (empty($pekerjaan)) {
                return 'Lainnya';
            }

            $pekerjaan = strtoupper(trim($pekerjaan));

            $mapping = [
                'PNS' => 'PNS',
                'PEGAWAI NEGERI' => 'PNS',
                'ASN' => 'PNS',
                
                'KARYAWAN' => 'Karyawan',
                'KARYAWAN SWASTA' => 'Karyawan',
                'PEGAWAI SWASTA' => 'Karyawan',
                'STAFF' => 'Karyawan',
                
                'WIRASWASTA' => 'Wiraswasta',
                'WIRAUSAHA' => 'Wiraswasta',
                'PENGUSAHA' => 'Wiraswasta',
                'PEDAGANG' => 'Wiraswasta',
                'USAHA' => 'Wiraswasta',
                
                'BURUH' => 'Buruh',
                'PEKERJA' => 'Buruh',
                'TUKANG' => 'Buruh',
                
                'PETANI' => 'Petani',
                'NELAYAN' => 'Petani',
                'PETERNAK' => 'Petani',
            ];

            if (isset($mapping[$pekerjaan])) {
                return $mapping[$pekerjaan];
            }

            foreach ($mapping as $keyword => $mlValue) {
                if (stripos($pekerjaan, $keyword) !== false) {
                    return $mlValue;
                }
            }

            return 'Lainnya';
        };

        $mapPendidikan = function ($pendidikan) {
            if (empty($pendidikan)) {
                return 'SMA'; 
            }

            $pendidikan = strtoupper(trim($pendidikan));

            $mapping = [
                'SD' => 'SD',
                'SMP' => 'SMP',
                'SMA' => 'SMA',
                'SMK' => 'SMA',
                'SMA/SMK' => 'SMA',
                'D3' => 'Diploma',
                'D4' => 'Diploma',
                'DIPLOMA' => 'Diploma',
                'S1' => 'S1',
                'S2' => 'S2',
                'S3' => 'S3',
            ];

            return $mapping[$pendidikan] ?? 'SMA';
        };

        $mapTujuanPinjaman = function ($tujuan) {
            if (empty($tujuan)) {
                return 'Lainnya';
            }

            $tujuan = trim($tujuan);

            $mapping = [
                'Modal Usaha' => 'Modal Usaha',
                'Pendidikan' => 'Pendidikan',
                'Renovasi' => 'Renovasi',
                'Renovasi Rumah' => 'Renovasi',
                'Kesehatan' => 'Darurat',
                'Darurat' => 'Darurat',
                'Pernikahan' => 'Darurat',
                'Konsumtif' => 'Konsumtif',
                'Investasi' => 'Lainnya',
                'Lainnya' => 'Lainnya',
            ];

            return $mapping[$tujuan] ?? 'Lainnya';
        };

        $mapRiwayatTunggakan = function ($tunggakan) {
            if (empty($tunggakan)) {
                return 'Tidak';
            }

            $tunggakan = trim($tunggakan);

            switch ($tunggakan) {
                case 'Tidak':
                case 'Tidak Pernah':
                    return 'Tidak';
                case 'Pernah':
                    return 'Pernah';
                case 'Sering':
                    return 'Sering';
                default:
                    return 'Tidak';
            }
        };

        $mapJaminan = function ($jaminan) {
            if (empty($jaminan) || trim($jaminan) === '' || strtolower(trim($jaminan)) === 'tidak') {
                return 'Tidak'; 
            }
            return 'Ada';
        };

        $mapStatusTempatTinggal = function ($status) {
            if (empty($status)) {
                return 'Milik Sendiri';
            }

            $status = trim($status);

            $validValues = ['Milik Sendiri', 'Sewa', 'Kontrak', 'Milik Orang Tua'];

            if (in_array($status, $validValues)) {
                return $status;
            }

            if ($status === 'Rumah Orang Tua') {
                return 'Milik Orang Tua';
            }

            return 'Milik Sendiri';
        };

        return [
            'Umur' => (int) ($inputData['umur'] ?? $inputData['anggota']->umur ?? 25),
            
            'Status_Pernikahan' => $mapStatusPernikahan(
                $inputData['status_perkawinan'] ?? $inputData['anggota']->status_perkawinan ?? ''
            ),
            
            'Jumlah_Tanggungan' => (int) ($inputData['jumlah_tanggungan'] ?? $inputData['anggota']->jumlah_tanggungan ?? 0),
            
            'Pendidikan' => $mapPendidikan(
                $inputData['pendidikan'] ?? $inputData['anggota']->pendidikan ?? 'SMA'
            ),
            
            'Jenis_Pekerjaan' => $mapPekerjaan(
                $inputData['pekerjaan'] ?? $inputData['anggota']->pekerjaan ?? ''
            ),
            
            'Lama_Bekerja_Tahun' => (float) (
                $inputData['lama_bekerja_tahun'] ?? 
                $inputData['Lama_Bekerja_Tahun'] ?? 
                $inputData['anggota']->lama_bekerja_tahun ?? 
                1.0
            ),
            
            'Pendapatan_Bulanan' => (float) ($inputData['pendapatan_bulanan'] ?? $inputData['anggota']->pendapatan_bulanan ?? 0),
            
            'Pengeluaran_Bulanan' => (float) ($inputData['pengeluaran_bulanan'] ?? $inputData['anggota']->pengeluaran_bulanan ?? 0),
            
            'Skor_Kredit' => (int) ($inputData['skor_kredit'] ?? $inputData['anggota']->skor_kredit ?? 50),
            
            'Jumlah_Pinjaman' => (float) ($inputData['jumlah_pinjaman'] ?? $inputData['anggota']->jumlah_pinjaman ?? 0),
            
            'Lama_Tenor_Bulan' => (int) (
                $inputData['tenor'] ?? 
                $inputData['Lama_Tenor_Bulan'] ?? 
                $inputData['anggota']->tenor ?? 
                12
            ),
            
            'Riwayat_Tunggakan' => $mapRiwayatTunggakan(
                $inputData['riwayat_tunggakan'] ?? $inputData['anggota']->riwayat_tunggakan ?? 'Tidak'
            ),
            
            'Jaminan' => $mapJaminan(
                $inputData['jaminan'] ?? $inputData['anggota']->jaminan ?? null
            ),
            
            'Tujuan_Pinjaman' => $mapTujuanPinjaman(
                $inputData['tujuan_pinjaman'] ?? $inputData['anggota']->tujuan_pinjaman ?? 'Modal Usaha'
            ),
            
            'Status_Tempat_Tinggal' => $mapStatusTempatTinggal(
                $inputData['status_tempat_tinggal'] ?? $inputData['anggota']->status_tempat_tinggal ?? 'Milik Sendiri'
            ),
        ];
    }

    private function runPythonScript(array $mappedData): array
    {
        try {
            if (!file_exists($this->pythonScriptPath)) {
                throw new Exception("Python script not found: {$this->pythonScriptPath}");
            }
            if (!file_exists($this->modelPath)) {
                 throw new Exception("ML model file not found: {$this->modelPath}");
            }

            $inputJson = json_encode($mappedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $tempFile = storage_path('app/temp_ml_input_' . uniqid() . '.json'); 
            file_put_contents($tempFile, $inputJson, LOCK_EX);

            $pythonCommand = $this->pythonPath;
            
            $command = sprintf(
                '"%s" "%s" "%s" "%s"',
                $pythonCommand,
                $this->pythonScriptPath,
                $this->modelPath, 
                $tempFile
            );

            Log::info('🤖 MLDataScrapper: Executing Python command', [
                'command' => $command
            ]);

            $environmentVars = [];
            
            $userProfileValue = $_SERVER['USERPROFILE'] ?? getenv('USERPROFILE');
            if ($userProfileValue !== false && $userProfileValue !== null) {
                $environmentVars['USERPROFILE'] = $userProfileValue;
            }

            $systemRoot = $_SERVER['SystemRoot'] ?? getenv('SystemRoot');
            if ($systemRoot) $environmentVars['SystemRoot'] = $systemRoot;
            
            $pathEnv = $_SERVER['PATH'] ?? getenv('PATH');
            if ($pathEnv) $environmentVars['PATH'] = $pathEnv;

            $process = Process::timeout($this->timeout)
                ->env($environmentVars) 
                ->run($command);

            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            if (!$process->failed()) {
                $output = trim($process->output());
                
                Log::info('🤖 MLDataScrapper: Raw Python output', ['output' => $output]);
                
                $result = json_decode($output, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $lines = explode("\n", $output);
                    $lastLine = end($lines);
                    $result = json_decode($lastLine, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception("Invalid JSON from Python script. Raw output: " . substr($output, 0, 200) . "...");
                    }
                }

                if (is_array($result) && isset($result['success']) && $result['success']) {
                    Log::info('🤖 MLDataScrapper: Python script call successful', $result);
                    return [
                        'success' => true,
                        'prediction' => $result['prediction'],
                        'confidence' => $result['confidence'] ?? null,
                    ];
                } else {
                    $errorMessage = $result['error'] ?? 'Unknown error from Python script';
                    Log::error('🤖 MLDataScrapper: Python script returned error', ['error' => $errorMessage]);
                    return [
                        'success' => false,
                        'error' => $errorMessage,
                    ];
                }
            } else {
                $errorOutput = $process->errorOutput();
                Log::error('🤖 MLDataScrapper: Python script execution failed', [
                    'error' => $errorOutput,
                    'output' => $process->output(),
                ]);
                return [
                    'success' => false,
                    'error' => 'Python script execution failed: ' . $errorOutput,
                ];
            }

        } catch (\Throwable $e) {
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
            
            Log::error('🤖 MLDataScrapper: Exception during Python script call', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => 'Exception during Python script call: ' . $e->getMessage(),
            ];
        }
    }
}