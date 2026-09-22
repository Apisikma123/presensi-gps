<?php

namespace App\Services;

use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
     * Get the specific layer for a feature, level, and context.
     * Simple client model: single-tier admin approval.
     */
    public function getLayer($feature, $level, $kodeDept = null, $kodeJabatan = null, $kodeCabang = null)
    {
        return null;
    }

    /**
     * Check if a user can approve the current step.
     * Supports delegation: karyawan with linked approval admin can approve using admin's role.
     *
     * @param string $feature
     * @param int $currentLevel
     * @param string $userRole
     * @param string|null $kodeDept
     * @param string|null $kodeJabatan
     * @param User|null $user The authenticated user (needed for delegation check)
     * @param string|null $kodeCabang
     * @return bool
     */
    public function canApprove($feature, $currentLevel, $userRole, $kodeDept = null, $kodeJabatan = null, $user = null, $kodeCabang = null)
    {
        // Get the rule that applies to this context for the current level
        $rule = $this->getLayer($feature, $currentLevel, $kodeDept, $kodeJabatan, $kodeCabang);

        if (!$rule) {
            // Default MVP: If no multi-tier rule is configured, allow direct approval without errors
            return true;
        }

        // Direct role match
        if ($userRole === $rule->role_name) {
            return true;
        }

        // Check via linked approval admin (delegation)
        if ($user && $userRole === 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if ($userkaryawan && $userkaryawan->approval_admin_id) {
                $admin = User::find($userkaryawan->approval_admin_id);
                if ($admin) {
                    $adminRole = $admin->getRoleNames()->first();
                    return $adminRole === $rule->role_name;
                }
            }
        }

        return false;
    }

    /**
     * Get the approval admin ID for delegation.
     * If user is karyawan with linked admin, return admin's ID.
     * Otherwise return the user's own ID.
     *
     * @param User $user
     * @return int
     */
    public function getApprovalUserId($user)
    {
        if ($user->hasRole('karyawan')) {
            $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
            if ($userkaryawan && $userkaryawan->approval_admin_id) {
                return $userkaryawan->approval_admin_id;
            }
        }
        return $user->id;
    }

    /**
     * Check if the authenticated user is the applicant themselves (prevent self-approval).
     *
     * @param User|null $user
     * @param string|null $applicantNik
     * @return bool
     */
    public function isSelfApproval($user, $applicantNik): bool
    {
        if (!$user || empty($applicantNik)) {
            return false;
        }

        $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
        if ($userkaryawan && !empty($userkaryawan->nik) && $userkaryawan->nik === $applicantNik) {
            return true;
        }

        if (!empty($user->username) && $user->username === $applicantNik) {
            return true;
        }

        return false;
    }
}
