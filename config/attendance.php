<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attendance Photo Storage & Long-Term Archiving
    |--------------------------------------------------------------------------
    |
    | Hot storage: Photos (0-12 months) remain as individual files on disk.
    | Cold storage: Photos older than 'hot_months' are safely archived monthly
    | into private ZIP archives (storage/app/private/attendance-archive/{YYYY-MM}.zip).
    | The database attendance record (date, time, status, audit) is NEVER deleted.
    |
    */
    'archive_enabled' => (bool) env('ATTENDANCE_PHOTO_ARCHIVE_ENABLED', true),
    'hot_months' => (int) env('ATTENDANCE_PHOTO_HOT_MONTHS', 12),
    'archive_disk_path' => storage_path('app/private/attendance-archive'),

    // Backward compatibility alias for existing storage audit commands
    'photo_retention_months' => (int) env('ATTENDANCE_PHOTO_HOT_MONTHS', env('ATTENDANCE_PHOTO_RETENTION_MONTHS', 12)),

    /*
    |--------------------------------------------------------------------------
    | Sick / Permission Attachment Retention Period (Months)
    |--------------------------------------------------------------------------
    |
    | Kept separate from attendance photos to prevent premature archiving or deletion
    | of legal / medical proof documents (surat dokter / SID).
    |
    */
    'leave_attachment_retention_months' => (int) env('LEAVE_ATTACHMENT_RETENTION_MONTHS', 24),
];
