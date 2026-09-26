<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Division::with('departemen')->withCount('karyawan');

        if ($request->filled('nama_divisi')) {
            $query->where('nama_divisi', 'like', '%' . $request->nama_divisi . '%');
        }

        if ($request->filled('kode_dept')) {
            $query->where('kode_dept', $request->kode_dept);
        }

        $divisi = $query->orderBy('kode_dept')->orderBy('nama_divisi')->paginate(15);
        $departemen = Departemen::orderBy('nama_dept')->get();

        return view('datamaster.divisi.index', compact('divisi', 'departemen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:10|unique:divisions,kode_divisi',
            'nama_divisi' => 'required|string|max:100',
            'kode_dept' => 'nullable|string|max:3|exists:departemen,kode_dept',
        ]);

        Division::create([
            'kode_divisi' => strtoupper(trim($request->kode_divisi)),
            'nama_divisi' => trim($request->nama_divisi),
            'kode_dept' => $request->kode_dept ?: null,
            'is_active' => true,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Divisi baru berhasil ditambahkan');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $divisi = Division::findOrFail($id);
        $departemen = Departemen::orderBy('nama_dept')->get();

        return view('datamaster.divisi.edit', compact('divisi', 'departemen'));
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $divisi = Division::findOrFail($id);

        $request->validate([
            'nama_divisi' => 'required|string|max:100',
            'kode_dept' => 'nullable|string|max:3|exists:departemen,kode_dept',
            'is_active' => 'nullable|boolean',
        ]);

        $divisi->update([
            'nama_divisi' => trim($request->nama_divisi),
            'kode_dept' => $request->kode_dept ?: null,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Data divisi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $divisi = Division::findOrFail($id);

        if ($divisi->karyawan()->count() > 0) {
            return redirect()->route('divisi.index')->with('error', 'Divisi tidak dapat dihapus karena masih digunakan oleh karyawan aktif');
        }

        $divisi->delete();
        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dihapus');
    }
}
