<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Update Server URL
    |--------------------------------------------------------------------------
    |
    | URL server untuk mengecek update terbaru. Jika null, akan menggunakan
    | database lokal untuk mengecek update.
    |
    */
    'server_url' => env('UPDATE_SERVER_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Auto Check Update
    |--------------------------------------------------------------------------
    |
    | Apakah aplikasi akan otomatis mengecek update secara berkala.
    |
    */
    'auto_check' => env('UPDATE_AUTO_CHECK', false),

    /*
    |--------------------------------------------------------------------------
    | Check Interval (days)
    |--------------------------------------------------------------------------
    |
    | Interval dalam hari untuk auto check update.
    |
    */
    'check_interval' => env('UPDATE_CHECK_INTERVAL', 7),

    /*
    |--------------------------------------------------------------------------
    | Backup Before Update
    |--------------------------------------------------------------------------
    |
    | Apakah akan backup database dan files sebelum update.
    |
    */
    'backup_before_update' => env('UPDATE_BACKUP_BEFORE_UPDATE', true),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificate when connecting to update server.
    |
    */
    'verify_ssl' => env('UPDATE_VERIFY_SSL', true),

    /*
    |--------------------------------------------------------------------------
    | Cryptographic Digital Signature Verification (SEC-001)
    |--------------------------------------------------------------------------
    |
    | Enforce asymmetric cryptographic digital signature verification on remote
    | update packages. Updates fail closed if the signature is missing or invalid.
    |
    */
    'require_signature' => true,

    /*
    | Signature algorithm: 'ed25519' (preferred, libsodium) or 'openssl'
    */
    'algorithm' => env('UPDATE_SIGNATURE_ALGORITHM', 'ed25519'),

    /*
    | Public key for client verification: either raw string / base64 or file path.
    */
    'public_key' => env('UPDATE_PUBLIC_KEY', null),
    'public_key_path' => env('UPDATE_PUBLIC_KEY_PATH', storage_path('keys/update_public.key')),

    /*
    | Server private key: used ONLY on the update/build server to sign packages.
    | NEVER store or deploy the private key to client installations.
    */
    'server_private_key' => env('UPDATE_SERVER_PRIVATE_KEY', null),
    'server_private_key_path' => env('UPDATE_SERVER_PRIVATE_KEY_PATH', storage_path('keys/update_server_private.key')),

    /*
    | Allowed hostnames for downloading updates.
    | If empty, defaults to the host configured in 'server_url'.
    */
    'allowed_hosts' => array_values(array_filter(array_map('trim', explode(',', env('UPDATE_ALLOWED_HOSTS', ''))))),
];
