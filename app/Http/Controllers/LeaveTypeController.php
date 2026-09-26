<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveType::withCount('quotas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $leaveTypes = $query->orderBy('name')->paginate(15);

        return view('datamaster.leave_types.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('datamaster.leave_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:leave_types,code',
            'name' => 'required|string|max:100',
            'quota_type' => 'required|string|in:ANNUAL,MONTHLY,UNLIMITED',
            'default_quota' => 'required|numeric|min:0|max:365',
            'gender_restriction' => 'nullable|string|in:M,P',
            'min_notice_days' => 'nullable|integer|min:0|max:90',
            'max_consecutive_days' => 'nullable|integer|min:1|max:365',
        ]);

        LeaveType::create([
            'code' => strtoupper(trim($request->code)),
            'name' => trim($request->name),
            'is_paid' => $request->has('is_paid'),
            'requires_attachment' => $request->has('requires_attachment'),
            'requires_approval' => $request->has('requires_approval'),
            'uses_quota' => $request->has('uses_quota'),
            'quota_type' => $request->quota_type,
            'default_quota' => (float) $request->default_quota,
            'gender_restriction' => $request->gender_restriction ?: null,
            'min_notice_days' => (int) ($request->min_notice_days ?? 0),
            'max_consecutive_days' => $request->max_consecutive_days ? (int) $request->max_consecutive_days : null,
            'is_active' => true,
        ]);

        return redirect()->route('leave_types.index')->with('success', 'Jenis cuti baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $leaveType = LeaveType::findOrFail($decryptedId);

        return view('datamaster.leave_types.edit', compact('leaveType'));
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $leaveType = LeaveType::findOrFail($decryptedId);

        $request->validate([
            'code' => 'required|string|max:20|unique:leave_types,code,' . $leaveType->id,
            'name' => 'required|string|max:100',
            'quota_type' => 'required|string|in:ANNUAL,MONTHLY,UNLIMITED',
            'default_quota' => 'required|numeric|min:0|max:365',
            'gender_restriction' => 'nullable|string|in:M,P',
            'min_notice_days' => 'nullable|integer|min:0|max:90',
            'max_consecutive_days' => 'nullable|integer|min:1|max:365',
        ]);

        $leaveType->update([
            'code' => strtoupper(trim($request->code)),
            'name' => trim($request->name),
            'is_paid' => $request->has('is_paid'),
            'requires_attachment' => $request->has('requires_attachment'),
            'requires_approval' => $request->has('requires_approval'),
            'uses_quota' => $request->has('uses_quota'),
            'quota_type' => $request->quota_type,
            'default_quota' => (float) $request->default_quota,
            'gender_restriction' => $request->gender_restriction ?: null,
            'min_notice_days' => (int) ($request->min_notice_days ?? 0),
            'max_consecutive_days' => $request->max_consecutive_days ? (int) $request->max_consecutive_days : null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('leave_types.index')->with('success', 'Data jenis cuti berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $leaveType = LeaveType::findOrFail($decryptedId);

        if ($leaveType->quotas()->count() > 0) {
            return redirect()->route('leave_types.index')->with('error', 'Jenis cuti tidak dapat dihapus karena sudah memiliki saldo kuota karyawan.');
        }

        $leaveType->delete();

        return redirect()->route('leave_types.index')->with('success', 'Jenis cuti berhasil dihapus.');
    }
}
