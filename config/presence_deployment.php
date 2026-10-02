<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Presence Canonical Deployment Configuration
    |--------------------------------------------------------------------------
    | Managed exclusively by Developer / Vendor.
    | Client Super Admin cannot modify these values through Web UI.
    |
    | Supported packages:
    | FNB_SMALL, FNB_STANDARD, FNB_PRO, RETAIL_SMALL, OFFICE_STANDARD, FULL_HR, CUSTOM
    */
    'canonical_package' => env('PRESENCE_PACKAGE', 'FULL_HR'),

    /*
    | Deployment profile locked state (true = protected)
    */
    'deployment_locked' => env('PRESENCE_DEPLOYMENT_LOCKED', true),

    /*
    | Developer Setup Console Enable Flag (false by default in production)
    | Set to true temporarily only during onboarding on shared hosting without SSH.
    | When false, ALL /vendor/deployment-* routes return 404.
    */
    'vendor_setup_enabled' => env('PRESENCE_VENDOR_SETUP_ENABLED', false),

    /*
    | Developer/Vendor Maintenance Authorization Key for Shared Hosting.
    | Set in .env as PRESENCE_VENDOR_KEY. Must never be exposed or logged.
    */
    'vendor_key' => env('PRESENCE_VENDOR_KEY', null),
];
