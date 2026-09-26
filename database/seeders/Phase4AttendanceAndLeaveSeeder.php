<?php

namespace Database\Seeders;

use App\Models\AttendancePolicy;
use App\Models\Cuti;
use App\Models\LeaveType;
use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase4AttendanceAndLeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Default Attendance Policy
        AttendancePolicy::firstOrCreate(
            ['is_default' => true],
            [
                'name' => 'Kebijakan Presensi Standar Perusahaan',
                'require_gps' => true,
                'require_face_recognition' => true,
                'require_photo' => true,
                'allow_late_tolerance_minutes' => 5,
                'max_out_of_radius_meters' => 50,
                'allow_out_of_radius' => false,
                'allow_overnight' => true,
                'enable_early_checkout_penalty' => false,
                'description' => 'Aturan presensi standar nasional dengan validasi GPS geofencing dan biometrik wajah.',
            ]
        );

        // 2. Statutory Indonesian Leave Types (UU Ketenagakerjaan No. 13/2003 & Cipta Kerja)
        $leaveTypes = [
            [
                'code' => 'CT',
                'name' => 'Cuti Tahunan',
                'is_paid' => true,
                'requires_attachment' => false,
                'requires_approval' => true,
                'uses_quota' => true,
                'quota_type' => 'ANNUAL',
                'default_quota' => 12.0,
                'gender_restriction' => null,
                'min_notice_days' => 3,
                'max_consecutive_days' => 12,
                'is_active' => true,
            ],
            [
                'code' => 'CS',
                'name' => 'Cuti Sakit',
                'is_paid' => true,
                'requires_attachment' => true,
                'requires_approval' => true,
                'uses_quota' => false,
                'quota_type' => 'UNLIMITED',
                'default_quota' => 0.0,
                'gender_restriction' => null,
                'min_notice_days' => 0,
                'max_consecutive_days' => null,
                'is_active' => true,
            ],
            [
                'code' => 'CM',
                'name' => 'Cuti Melahirkan / Bersalin',
                'is_paid' => true,
                'requires_attachment' => true,
                'requires_approval' => true,
                'uses_quota' => true,
                'quota_type' => 'ANNUAL',
                'default_quota' => 90.0,
                'gender_restriction' => 'P', // Perempuan
                'min_notice_days' => 14,
                'max_consecutive_days' => 90,
                'is_active' => true,
            ],
            [
                'code' => 'CK',
                'name' => 'Cuti Keguguran',
                'is_paid' => true,
                'requires_attachment' => true,
                'requires_approval' => true,
                'uses_quota' => true,
                'quota_type' => 'ANNUAL',
                'default_quota' => 45.0,
                'gender_restriction' => 'P',
                'min_notice_days' => 0,
                'max_consecutive_days' => 45,
                'is_active' => true,
            ],
            [
                'code' => 'CN',
                'name' => 'Cuti Menikah (Pekerja)',
                'is_paid' => true,
                'requires_attachment' => true,
                'requires_approval' => true,
                'uses_quota' => true,
                'quota_type' => 'ANNUAL',
                'default_quota' => 3.0,
                'gender_restriction' => null,
                'min_notice_days' => 7,
                'max_consecutive_days' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'CKM',
                'name' => 'Cuti Menikahkan Anak',
                'is_paid' => true,
                'requires_attachment' => true,
                'requires_approval' => true,
                'uses_quota' => true,
                'quota_type' => 'ANNUAL',
                'default_quota' => 2.0,
                'gender_restriction' => null,
                'min_notice_days' => 5,
                'max_consecutive_days' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'CKH',
                'name' => 'Cuti Khitanan / Baptis Anak',
                'is_paid' => true,
                'requires_attachment' => true,
                'requires_approval' => true,
                'uses_quota' => true,
                'quota_type' => 'ANNUAL',
                'default_quota' => 2.0,
                'gender_restriction' => null,
                'min_notice_days' => 5,
                'max_consecutive_days' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'CD',
                'name' => 'Cuti Kematian Keluarga Inti',
                'is_paid' => true,
                'requires_attachment' => false,
                'requires_approval' => true,
                'uses_quota' => true,
                'quota_type' => 'ANNUAL',
                'default_quota' => 2.0,
                'gender_restriction' => null,
                'min_notice_days' => 0,
                'max_consecutive_days' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'IP',
                'name' => 'Izin Tidak Masuk Kerja (Pribadi)',
                'is_paid' => false,
                'requires_attachment' => false,
                'requires_approval' => true,
                'uses_quota' => false,
                'quota_type' => 'UNLIMITED',
                'default_quota' => 0.0,
                'gender_restriction' => null,
                'min_notice_days' => 1,
                'max_consecutive_days' => null,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::updateOrCreate(['code' => $type['code']], $type);

            // Sync to legacy cuti table
            $legacyCode = strtoupper(substr($type['code'], 0, 3));
            Cuti::updateOrCreate(
                ['kode_cuti' => $legacyCode],
                [
                    'jenis_cuti' => $type['name'],
                    'jumlah_hari' => (int) $type['default_quota'],
                ]
            );
        }

        // 3. Permissions & Groups
        $permissionDefinitions = [
            'Kebijakan Presensi' => [
                'attendance_policy.index',
                'attendance_policy.edit',
            ],
            'Master & Kuota Cuti' => [
                'leave_types.index',
                'leave_types.create',
                'leave_types.edit',
                'leave_types.delete',
                'leave_quotas.index',
                'leave_quotas.adjust',
            ],
        ];

        $roles = Role::whereIn('name', ['super admin', 'master admin'])->get();

        foreach ($permissionDefinitions as $groupName => $perms) {
            $group = Permission_group::firstOrCreate(['name' => $groupName]);

            foreach ($perms as $permName) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permName],
                    ['id_permission_group' => $group->id]
                );

                foreach ($roles as $role) {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
