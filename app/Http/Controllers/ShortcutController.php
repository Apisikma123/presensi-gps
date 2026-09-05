<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;

class ShortcutController extends Controller
{
    public function index()
    {
        $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
        $karyawan = null;
        if ($userkaryawan) {
            $karyawan = Karyawan::where('nik', $userkaryawan->nik)
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->first();
        }

        return view('shortcut.index', compact('karyawan'));
    }
}
