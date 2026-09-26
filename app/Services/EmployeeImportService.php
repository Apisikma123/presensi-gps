<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\EmployeeSalaryAssignment;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\SalaryComponent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeImportService
{
    /**
     * Get sample CSV template content
     */
    public function generateTemplate(): string
    {
        $headers = [
            'nik',
            'nama_karyawan',
            'kode_dept',
            'kode_cabang',
            'kode_jabatan',
            'jenis_kelamin',
            'tanggal_masuk',
            'no_hp',
            'ptkp_status',
            'gaji_pokok',
        ];

        $sampleRow = [
            '1099',
            'Budi Setiawan',
            'MKT',
            'BDG',
            'J04',
            'L',
            date('Y-m-d'),
            '081234567890',
            'TK/0',
            '5000000',
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        fputcsv($output, $sampleRow);
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv ?: '';
    }

    /**
     * Import employees from CSV file
     */
    public function importFromCsv(string $filePath, ?int $userId = null): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [
                'success_count' => 0,
                'failed_count' => 0,
                'errors' => ['Berkas CSV tidak ditemukan atau tidak dapat dibaca.'],
            ];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [
                'success_count' => 0,
                'failed_count' => 0,
                'errors' => ['Gagal membuka berkas CSV.'],
            ];
        }

        // Detect delimiter (comma or semicolon)
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        $headers = fgetcsv($handle, 1000, $delimiter);
        if (!$headers) {
            fclose($handle);
            return [
                'success_count' => 0,
                'failed_count' => 0,
                'errors' => ['Format berkas CSV tidak valid atau kosong.'],
            ];
        }

        // Normalize header strings
        $normalizedHeaders = array_map(fn($h) => strtolower(trim((string) $h)), $headers);

        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        $rowNumber = 1;

        $deptCodes = Departemen::pluck('kode_dept')->flip()->toArray();
        $cabangCodes = Cabang::pluck('kode_cabang')->flip()->toArray();
        $jabatanCodes = Jabatan::pluck('kode_jabatan')->flip()->toArray();
        $defaultDept = !empty($deptCodes) ? array_key_first($deptCodes) : 'HRD';
        $defaultCabang = !empty($cabangCodes) ? array_key_first($cabangCodes) : 'JKT';
        $defaultJabatan = !empty($jabatanCodes) ? array_key_first($jabatanCodes) : 'J04';
        $basicSalaryComponent = SalaryComponent::where('code', 'BASIC_SALARY')->first();

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNumber++;
            if (empty(array_filter($row))) {
                continue; // Skip empty rows
            }

            $data = array_combine(
                array_slice($normalizedHeaders, 0, count($row)),
                array_slice($row, 0, count($normalizedHeaders))
            );

            $nik = trim($data['nik'] ?? '');
            $nama = trim($data['nama_karyawan'] ?? '');

            if (empty($nik) || empty($nama)) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: NIK dan Nama Karyawan wajib diisi.";
                continue;
            }

            if (Karyawan::where('nik', $nik)->exists()) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: NIK '{$nik}' sudah terdaftar dalam sistem.";
                continue;
            }

            $kodeDept = trim($data['kode_dept'] ?? '');
            if (empty($kodeDept) || !isset($deptCodes[$kodeDept])) {
                $kodeDept = $defaultDept;
            }

            $kodeCabang = trim($data['kode_cabang'] ?? '');
            if (empty($kodeCabang) || !isset($cabangCodes[$kodeCabang])) {
                $kodeCabang = $defaultCabang;
            }

            $kodeJabatan = trim($data['kode_jabatan'] ?? '');
            if (empty($kodeJabatan) || !isset($jabatanCodes[$kodeJabatan])) {
                $kodeJabatan = $defaultJabatan;
            }

            $tglMasuk = trim($data['tanggal_masuk'] ?? '');
            try {
                $tglMasuk = $tglMasuk ? Carbon::parse($tglMasuk)->toDateString() : Carbon::today()->toDateString();
            } catch (\Throwable $e) {
                $tglMasuk = Carbon::today()->toDateString();
            }

            $gender = strtoupper(trim($data['jenis_kelamin'] ?? 'L'));
            if (!in_array($gender, ['L', 'P'])) {
                $gender = 'L';
            }

            $ptkp = strtoupper(trim($data['ptkp_status'] ?? 'TK/0'));
            $gajiPokok = (float) str_replace(['.', ','], '', trim($data['gaji_pokok'] ?? '0'));

            try {
                DB::transaction(function () use ($nik, $nama, $kodeDept, $kodeCabang, $kodeJabatan, $gender, $tglMasuk, $data, $ptkp, $gajiPokok, $basicSalaryComponent, $userId) {
                    Karyawan::create([
                        'nik' => $nik,
                        'nama_karyawan' => $nama,
                        'kode_dept' => $kodeDept,
                        'kode_cabang' => $kodeCabang,
                        'kode_jabatan' => $kodeJabatan,
                        'jenis_kelamin' => $gender,
                        'tanggal_masuk' => $tglMasuk,
                        'no_hp' => trim($data['no_hp'] ?? ''),
                        'ptkp_status' => $ptkp,
                        'status_aktif_karyawan' => '1',
                    ]);

                    // Assign Basic Salary Component if specified
                    if ($gajiPokok > 0 && $basicSalaryComponent) {
                        EmployeeSalaryAssignment::updateOrCreate(
                            ['nik' => $nik, 'salary_component_id' => $basicSalaryComponent->id],
                            [
                                'amount' => $gajiPokok,
                                'effective_date' => $tglMasuk,
                                'reason' => 'Import Awal Karyawan',
                                'approved_by' => $userId,
                            ]
                        );
                    }
                });

                $successCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber} (NIK {$nik}): " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Perform bulk attribute updates for employees
     */
    public function bulkUpdate(array $niks, array $attributes): int
    {
        $allowed = array_intersect_key($attributes, array_flip([
            'kode_dept',
            'kode_cabang',
            'kode_jabatan',
            'status_aktif_karyawan',
            'status_karyawan',
        ]));

        if (empty($allowed) || empty($niks)) {
            return 0;
        }

        return Karyawan::whereIn('nik', $niks)->update($allowed);
    }
}
