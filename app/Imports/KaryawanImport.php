<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KaryawanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cek apakah baris memiliki data atau kosong
        if ($this->isRowEmpty($row)) {
            return null; // Skip baris kosong
        }

        // Generate nik otomatis (9 digit)
        $nik = $this->generateNik();

        return new Karyawan([
            'nik' => $nik,
            'nik_show' => $row['nik'], // NIK dari Excel masuk ke nik_show
            'nama_karyawan' => $row['nama_karyawan'],
            'alamat' => $row['alamat'] ?? '-',
            'no_hp' => $row['no_hp'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'],
            'kode_cabang' => $row['kode_cabang'],
            'kode_dept' => $row['kode_dept'],
            'kode_jabatan' => $row['kode_jabatan'],
            'kode_jam_kerja' => !empty($row['kode_jam_kerja']) ? $row['kode_jam_kerja'] : 'JK01',
            'tanggal_masuk' => $this->convertDate($row['tanggal_masuk']),
            'status_karyawan' => $row['status_karyawan'] ?? 'T',
            'lock_location' => 0,
            'lock_jam_kerja' => 1,
            'status_aktif_karyawan' => $row['status_aktif_karyawan'] ?? 1,
            'password' => bcrypt(\Illuminate\Support\Str::password(12, numbers: true, symbols: false))
        ]);
    }

    /**
     * Konversi format tanggal dari Excel ke format Y-m-d
     * Menangani berbagai format tanggal yang mungkin dari Excel
     */
    private function convertDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        // Debug: Log format tanggal yang diterima
        Log::info('Date value received: ' . $dateValue . ' (Type: ' . gettype($dateValue) . ')');

        // Jika sudah dalam format Y-m-d, langsung return
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
            return $dateValue;
        }

        // Coba berbagai format yang mungkin dari Excel
        $formats = [
            'Y-m-d',      // 1993-10-01
            'd/m/Y',      // 01/10/1993
            'd-m-Y',      // 01-10-1993
            'm/d/Y',      // 10/01/1993
            'Y/m/d',      // 1993/10/01
            'd.m.Y',      // 01.10.1993
            'Y.m.d',      // 1993.10.01
        ];

        foreach ($formats as $format) {
            try {
                $carbon = Carbon::createFromFormat($format, $dateValue);
                if ($carbon) {
                    return $carbon->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Lanjut ke format berikutnya
                continue;
            }
        }

        // Jika semua format gagal, coba parse umum
        try {
            $carbon = Carbon::parse($dateValue);
            return $carbon->format('Y-m-d');
        } catch (\Exception $e) {
            // Coba handle Excel serial number (jika tanggal dikirim sebagai angka)
            if (is_numeric($dateValue)) {
                try {
                    // Excel serial number: 1 = 1900-01-01, 2 = 1900-01-02, dst
                    $excelDate = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($dateValue - 2);
                    return $excelDate->format('Y-m-d');
                } catch (\Exception $e2) {
                    Log::error('Failed to convert Excel serial date: ' . $dateValue);
                }
            }

            // Jika masih gagal, return null untuk menghindari error
            Log::error('Failed to convert date: ' . $dateValue . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cek apakah baris kosong atau tidak
     * Baris dianggap kosong jika field wajib tidak ada atau kosong
     */
    private function isRowEmpty(array $row)
    {
        // Field wajib yang harus ada untuk dianggap baris tidak kosong
        $requiredFields = [
            'nik',
            'nama_karyawan',
            'jenis_kelamin',
            'kode_cabang',
            'kode_dept',
            'kode_jabatan',
            'tanggal_masuk',
            'status_karyawan'
        ];

        // Cek apakah semua field wajib kosong atau tidak ada
        foreach ($requiredFields as $field) {
            if (!empty($row[$field]) && trim($row[$field]) !== '') {
                return false; // Ada data, baris tidak kosong
            }
        }

        return true; // Semua field kosong, baris kosong
    }

    /**
     * Generate NIK otomatis format YYMM + 5 digit urut per bulan
     * Sama dengan format di KaryawanController::store()
     */
    private function generateNik()
    {
        // Generate NIK format YYMM + 5 digit urut per bulan
        $tahun = date('y');
        $bulan = date('m');
        $prefix = $tahun . $bulan; // e.g., 2510

        $last = Karyawan::where('nik', 'like', $prefix . '%')
            ->orderBy('nik', 'desc')
            ->first();

        $lastNumber = 0;
        if ($last) {
            $lastNumber = (int)substr($last->nik, 4, 5);
        }
        $nextNumber = $lastNumber + 1;
        $nikAuto = $prefix . str_pad((string)$nextNumber, 5, '0', STR_PAD_LEFT);

        return $nikAuto;
    }

    public function rules(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $cabangRule = ['required', 'exists:cabang,kode_cabang'];
        $deptRule = ['required', 'exists:departemen,kode_dept'];

        if ($user && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepts = $user->getDepartemenCodes();
            $cabangRule[] = Rule::in(!empty($userCabangs) ? $userCabangs : ['INVALID']);
            $deptRule[] = Rule::in(!empty($userDepts) ? $userDepts : ['INVALID']);
        }

        return [
            'nik' => ['required', 'unique:karyawan,nik_show'],
            'nama_karyawan' => 'required',
            'no_hp' => 'nullable',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable',
            'kode_cabang' => $cabangRule,
            'kode_dept' => $deptRule,
            'kode_jabatan' => 'required|exists:jabatan,kode_jabatan',
            'kode_jam_kerja' => 'nullable|exists:presensi_jamkerja,kode_jam_kerja',
            'tanggal_masuk' => 'required',
            'status_karyawan' => 'required',
            'status_aktif_karyawan' => 'nullable|in:0,1'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_cabang.in' => 'Anda tidak memiliki hak akses untuk mengimpor karyawan ke cabang tersebut',
            'kode_dept.in' => 'Anda tidak memiliki hak akses untuk mengimpor karyawan ke departemen tersebut',
            'nik.required' => 'NIK harus diisi',
            'nik.unique' => 'NIK sudah terdaftar di sistem',
            'nama_karyawan.required' => 'Nama karyawan harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'kode_cabang.required' => 'Kode cabang harus diisi',
            'kode_cabang.exists' => 'Kode cabang tidak valid',
            'kode_dept.required' => 'Kode departemen harus diisi',
            'kode_dept.exists' => 'Kode departemen tidak valid',
            'kode_jabatan.required' => 'Kode jabatan harus diisi',
            'kode_jabatan.exists' => 'Kode jabatan tidak valid',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'status_karyawan.required' => 'Status karyawan harus diisi',
            'status_aktif_karyawan.in' => 'Status aktif harus 0 atau 1'
        ];
    }
}
