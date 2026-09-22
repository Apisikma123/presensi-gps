<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UpdateSignatureService
{
    /**
     * Build canonical payload for signature verification
     */
    public function getCanonicalPayload(string $sha256, string $version = ''): string
    {
        $sha256 = strtolower(trim($sha256));
        $version = trim($version);

        if (!empty($version)) {
            return "version:{$version}|sha256:{$sha256}";
        }

        return $sha256;
    }

    /**
     * Verify digital signature of an update package.
     *
     * @param string $sha256 The SHA-256 checksum of the update file
     * @param string|null $signature Base64 encoded digital signature
     * @param string $version The version of the update
     * @return bool
     * @throws \RuntimeException on missing public key or missing signature
     */
    public function verifySignature(string $sha256, ?string $signature, string $version = ''): bool
    {
        if (empty($signature)) {
            Log::warning('[SEC-001] Update signature verification failed: signature is missing');
            throw new \RuntimeException('Update package missing digital signature. Update rejected (fail-closed).');
        }

        $algorithm = strtolower(config('update.algorithm', 'ed25519'));

        if ($algorithm === 'ed25519') {
            return $this->verifyEd25519($sha256, $signature, $version);
        } elseif ($algorithm === 'openssl') {
            return $this->verifyOpenSSL($sha256, $signature, $version);
        }

        throw new \RuntimeException("Unsupported signature algorithm: {$algorithm}");
    }

    /**
     * Verify using Ed25519 (libsodium)
     */
    protected function verifyEd25519(string $sha256, string $signature, string $version): bool
    {
        if (!extension_loaded('sodium')) {
            throw new \RuntimeException('Sodium extension is required for Ed25519 signature verification');
        }

        $rawPublicKey = $this->getPublicKey();
        if (empty($rawPublicKey)) {
            Log::error('[SEC-001] Public key for update verification is not configured or missing');
            throw new \RuntimeException('Update verification public key is not configured. Update rejected (fail-closed).');
        }

        // Normalize public key to 32-byte binary
        $publicKey = $this->normalizePublicKey($rawPublicKey);
        if (strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            Log::error('[SEC-001] Invalid public key length: expected 32 bytes, got ' . strlen($publicKey));
            throw new \RuntimeException('Invalid Ed25519 public key format. Update rejected (fail-closed).');
        }

        // Decode signature from base64
        $binarySignature = base64_decode($signature, true);
        if ($binarySignature === false || strlen($binarySignature) !== SODIUM_CRYPTO_SIGN_BYTES) {
            Log::warning('[SEC-001] Malformed Ed25519 signature: invalid base64 or length');
            return false;
        }

        // Try canonical payload first (version:X|sha256:Y)
        $canonicalPayload = $this->getCanonicalPayload($sha256, $version);
        if (sodium_crypto_sign_verify_detached($binarySignature, $canonicalPayload, $publicKey)) {
            return true;
        }

        // Fallback: if version was included in canonical payload, try raw sha256 payload
        if (!empty($version)) {
            $rawPayload = strtolower(trim($sha256));
            if (sodium_crypto_sign_verify_detached($binarySignature, $rawPayload, $publicKey)) {
                return true;
            }
        }

        Log::warning('[SEC-001] Ed25519 digital signature verification failed for update package');
        return false;
    }

    /**
     * Verify using OpenSSL
     */
    protected function verifyOpenSSL(string $sha256, string $signature, string $version): bool
    {
        $rawPublicKey = $this->getPublicKey();
        if (empty($rawPublicKey)) {
            throw new \RuntimeException('Update verification public key is not configured. Update rejected (fail-closed).');
        }

        $binarySignature = base64_decode($signature, true);
        if ($binarySignature === false) {
            return false;
        }

        $canonicalPayload = $this->getCanonicalPayload($sha256, $version);
        $result = openssl_verify($canonicalPayload, $binarySignature, $rawPublicKey, OPENSSL_ALGO_SHA256);

        if ($result === 1) {
            return true;
        }

        if (!empty($version)) {
            $rawPayload = strtolower(trim($sha256));
            $result = openssl_verify($rawPayload, $binarySignature, $rawPublicKey, OPENSSL_ALGO_SHA256);
            if ($result === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sign an update package (Used on build / update server).
     *
     * @param string $sha256
     * @param string $version
     * @param string|null $customPrivateKey
     * @return string Base64 encoded signature
     */
    public function signPackage(string $sha256, string $version = '', ?string $customPrivateKey = null): string
    {
        $algorithm = strtolower(config('update.algorithm', 'ed25519'));

        if ($algorithm === 'ed25519') {
            return $this->signEd25519($sha256, $version, $customPrivateKey);
        } elseif ($algorithm === 'openssl') {
            return $this->signOpenSSL($sha256, $version, $customPrivateKey);
        }

        throw new \RuntimeException("Unsupported signature algorithm: {$algorithm}");
    }

    /**
     * Sign using Ed25519 (libsodium)
     */
    protected function signEd25519(string $sha256, string $version, ?string $customPrivateKey = null): string
    {
        if (!extension_loaded('sodium')) {
            throw new \RuntimeException('Sodium extension is required for Ed25519 signing');
        }

        $rawPrivateKey = $customPrivateKey ?? $this->getServerPrivateKey();
        if (empty($rawPrivateKey)) {
            throw new \RuntimeException('Server private key is not configured. Cannot sign package.');
        }

        $privateKey = $this->normalizePrivateKey($rawPrivateKey);
        if (strlen($privateKey) !== SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
            throw new \RuntimeException('Invalid Ed25519 private key length. Expected 64 bytes, got ' . strlen($privateKey));
        }

        $payload = $this->getCanonicalPayload($sha256, $version);
        $binarySignature = sodium_crypto_sign_detached($payload, $privateKey);

        return base64_encode($binarySignature);
    }

    /**
     * Sign using OpenSSL
     */
    protected function signOpenSSL(string $sha256, string $version, ?string $customPrivateKey = null): string
    {
        $rawPrivateKey = $customPrivateKey ?? $this->getServerPrivateKey();
        if (empty($rawPrivateKey)) {
            throw new \RuntimeException('Server private key is not configured. Cannot sign package.');
        }

        $payload = $this->getCanonicalPayload($sha256, $version);
        $binarySignature = '';

        $success = openssl_sign($payload, $binarySignature, $rawPrivateKey, OPENSSL_ALGO_SHA256);
        if (!$success) {
            throw new \RuntimeException('OpenSSL signature generation failed');
        }

        return base64_encode($binarySignature);
    }

    /**
     * Retrieve public key from config or file
     */
    public function getPublicKey(): ?string
    {
        // 1. Direct config / env
        $key = config('update.public_key');
        if (!empty($key)) {
            return trim($key);
        }

        // 2. File path
        $path = config('update.public_key_path');
        if (!empty($path) && File::exists($path)) {
            return trim(File::get($path));
        }

        return null;
    }

    /**
     * Retrieve server private key from config or file
     */
    public function getServerPrivateKey(): ?string
    {
        $key = config('update.server_private_key');
        if (!empty($key)) {
            return trim($key);
        }

        $path = config('update.server_private_key_path');
        if (!empty($path) && File::exists($path)) {
            return trim(File::get($path));
        }

        return null;
    }

    /**
     * Normalize public key to 32 bytes binary
     */
    public function normalizePublicKey(string $key): string
    {
        $key = trim($key);

        // Raw 32 bytes
        if (strlen($key) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            return $key;
        }

        // Hex encoded (64 chars)
        if (ctype_xdigit($key) && strlen($key) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES * 2) {
            return hex2bin($key);
        }

        // Base64 encoded
        $decoded = base64_decode($key, true);
        if ($decoded !== false && strlen($decoded) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            return $decoded;
        }

        return $key;
    }

    /**
     * Normalize private key to 64 bytes binary
     */
    public function normalizePrivateKey(string $key): string
    {
        $key = trim($key);

        // Raw 64 bytes
        if (strlen($key) === SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
            return $key;
        }

        // Hex encoded (128 chars)
        if (ctype_xdigit($key) && strlen($key) === SODIUM_CRYPTO_SIGN_SECRETKEYBYTES * 2) {
            return hex2bin($key);
        }

        // Base64 encoded
        $decoded = base64_decode($key, true);
        if ($decoded !== false && strlen($decoded) === SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
            return $decoded;
        }

        return $key;
    }

    /**
     * Generate fresh Ed25519 keypair
     *
     * @return array ['public_key' => base64, 'private_key' => base64]
     */
    public function generateKeyPair(): array
    {
        if (!extension_loaded('sodium')) {
            throw new \RuntimeException('Sodium extension is required for keypair generation');
        }

        $keypair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keypair);
        $secretKey = sodium_crypto_sign_secretkey($keypair);

        return [
            'public_key' => base64_encode($publicKey),
            'private_key' => base64_encode($secretKey),
        ];
    }
}
