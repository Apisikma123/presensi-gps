<?php

namespace App\Services;

use App\Models\AttendancePolicy;

class AttendancePolicyService
{
    protected ModuleFeatureService $featureService;

    public function __construct(ModuleFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    /**
     * Get active attendance policy
     */
    public function getPolicy(): AttendancePolicy
    {
        return AttendancePolicy::getActivePolicy();
    }

    /**
     * Check if GPS validation is enforced
     */
    public function isGpsRequired(): bool
    {
        if (!$this->featureService->isEnabled('gps')) {
            return false;
        }

        return $this->getPolicy()->require_gps;
    }

    /**
     * Check if Face Recognition AI is enforced
     */
    public function isFaceRecognitionRequired(): bool
    {
        if (!$this->featureService->isEnabled('face_recognition')) {
            return false;
        }

        return $this->getPolicy()->require_face_recognition;
    }

    /**
     * Check if attendance photo is required
     */
    public function isPhotoRequired(): bool
    {
        return $this->getPolicy()->require_photo;
    }

    /**
     * Get late arrival grace period in minutes
     */
    public function getLateToleranceMinutes(): int
    {
        return $this->getPolicy()->allow_late_tolerance_minutes;
    }

    /**
     * Check if check-in outside defined radius is allowed
     */
    public function isOutOfRadiusAllowed(): bool
    {
        return $this->getPolicy()->allow_out_of_radius;
    }

    /**
     * Max allowed deviation radius in meters
     */
    public function getMaxRadiusMeters(): int
    {
        return $this->getPolicy()->max_out_of_radius_meters;
    }
}
