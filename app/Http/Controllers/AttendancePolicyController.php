<?php

namespace App\Http\Controllers;

use App\Models\AttendancePolicy;
use Illuminate\Http\Request;

class AttendancePolicyController extends Controller
{
    public function index()
    {
        $policy = AttendancePolicy::getActivePolicy();

        return view('settings.attendance.index', compact('policy'));
    }

    public function update(Request $request)
    {
        $policy = AttendancePolicy::getActivePolicy();

        $request->validate([
            'name' => 'required|string|max:100',
            'allow_late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'max_out_of_radius_meters' => 'required|integer|min:10|max:1000',
            'description' => 'nullable|string',
        ]);

        $policy->update([
            'name' => trim($request->name),
            'require_gps' => $request->has('require_gps'),
            'require_face_recognition' => $request->has('require_face_recognition'),
            'require_photo' => $request->has('require_photo'),
            'allow_late_tolerance_minutes' => (int) $request->allow_late_tolerance_minutes,
            'max_out_of_radius_meters' => (int) $request->max_out_of_radius_meters,
            'allow_out_of_radius' => $request->has('allow_out_of_radius'),
            'allow_overnight' => $request->has('allow_overnight'),
            'enable_early_checkout_penalty' => $request->has('enable_early_checkout_penalty'),
            'description' => $request->description,
        ]);

        return redirect()->route('attendance_policy.index')->with('success', 'Kebijakan presensi kehadiran berhasil diperbarui.');
    }
}
