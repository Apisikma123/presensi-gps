<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = $user_karyawan ? Karyawan::where('nik', $user_karyawan->nik)->first() : null;
        $data['karyawan'] = $karyawan;
        $data['user'] = $user;
        return view('profile.index', $data);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = $user_karyawan ? Karyawan::where('nik', $user_karyawan->nik)->first() : null;

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'nama_karyawan' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'no_ktp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
        ]);

        try {
            if ($karyawan) {
                $data_foto = [];
                if ($request->hasfile('foto')) {
                    if (!empty($karyawan->foto) && Storage::disk('public')->exists('karyawan/' . $karyawan->foto)) {
                        Storage::disk('public')->delete('karyawan/' . $karyawan->foto);
                    }

                    $foto_name = \App\Helpers\ImageOptimizer::saveAsWebp(
                        $request->file('foto'),
                        'karyawan',
                        $karyawan->nik . "_" . time(),
                        80,
                        800
                    );
                    $data_foto = [
                        'foto' => $foto_name
                    ];
                }

                // Strict whitelist: employee can only update basic personal info
                $data_karyawan = [
                    'nama_karyawan' => $request->nama_karyawan,
                    'no_ktp' => $request->no_ktp,
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat,
                ];
                $data = array_merge($data_karyawan, $data_foto);
                Karyawan::where('nik', $karyawan->nik)->update($data);
            }

            $userData = ['name' => $request->nama_karyawan];
            if ($request->filled('email')) {
                $userData['email'] = $request->email;
            }
            if ($request->filled('username')) {
                $userData['username'] = $request->username;
            }
            User::where('id', $user->id)->update($userData);
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
    public function editprofile()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function updateprofile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'username' => $request->username,
            ];

            if ($request->filled('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            User::where('id', $user->id)->update($data);

            return Redirect::back()->with(messageSuccess('Profile Berhasil Diupdate'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
