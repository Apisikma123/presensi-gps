<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Convert and store an image (UploadedFile, binary string, or Base64) to optimized WebP format.
     *
     * @param mixed $input UploadedFile|string
     * @param string $folderPath e.g. 'uploads/absensi' or 'public/uploads/absensi'
     * @param string $filenameWithoutExt e.g. '12345-2026-09-02-in'
     * @param int $quality Compression quality (1-100), default 80
     * @param int $maxWidth Maximum width or height constraint (default 1080px)
     * @param string $disk Storage disk, default 'public'
     * @return string Generated filename (e.g. '12345-2026-09-02-in.webp')
     */
    public static function saveAsWebp($input, string $folderPath, string $filenameWithoutExt, int $quality = 80, int $maxWidth = 1080, string $disk = 'public'): string
    {
        $rawBinary = null;

        if ($input instanceof UploadedFile) {
            $rawBinary = file_get_contents($input->getRealPath());
        } elseif (is_string($input)) {
            if (str_contains($input, ';base64,')) {
                $parts = explode(';base64,', $input);
                $rawBinary = base64_decode(end($parts));
            } elseif (str_starts_with($input, 'data:image/') && str_contains($input, ',')) {
                $parts = explode(',', $input);
                $rawBinary = base64_decode(end($parts));
            } elseif (base64_encode(base64_decode($input, true)) === $input) {
                $rawBinary = base64_decode($input);
            } else {
                $rawBinary = $input;
            }
        }

        if (empty($rawBinary)) {
            Log::warning('ImageOptimizer: Empty image input received.');
            return $filenameWithoutExt . '.png';
        }

        // Clean and normalize folder path (prevent disk prefix duplication e.g. 'public/uploads' on 'public' disk)
        $folderPath = trim($folderPath, '/');
        if ($disk === 'public' && str_starts_with($folderPath, 'public/')) {
            $folderPath = substr($folderPath, 7);
        } elseif ($disk === 'private' && str_starts_with($folderPath, 'private/')) {
            $folderPath = substr($folderPath, 8);
        }

        $fileName = $filenameWithoutExt . '.webp';
        $fullPath = $folderPath ? ($folderPath . '/' . $fileName) : $fileName;

        // If GD is available, optimize and convert to WebP
        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            try {
                $image = @imagecreatefromstring($rawBinary);
                if ($image !== false) {
                    // Check and correct EXIF orientation (common on mobile phone camera uploads)
                    if (function_exists('exif_read_data')) {
                        try {
                            $stream = fopen('php://memory', 'r+');
                            fwrite($stream, $rawBinary);
                            rewind($stream);
                            $exif = @exif_read_data($stream);
                            fclose($stream);

                            if (!empty($exif['Orientation'])) {
                                switch ($exif['Orientation']) {
                                    case 3:
                                        $image = imagerotate($image, 180, 0);
                                        break;
                                    case 6:
                                        $image = imagerotate($image, -90, 0);
                                        break;
                                    case 8:
                                        $image = imagerotate($image, 90, 0);
                                        break;
                                }
                            }
                        } catch (\Throwable $e) {
                            // Non-fatal if EXIF cannot be parsed
                        }
                    }

                    $width = imagesx($image);
                    $height = imagesy($image);

                    // Auto-downscale if larger than maxWidth while maintaining aspect ratio
                    if ($width > $maxWidth || $height > $maxWidth) {
                        if ($width >= $height) {
                            $newWidth = $maxWidth;
                            $newHeight = (int) round(($height / $width) * $maxWidth);
                        } else {
                            $newHeight = $maxWidth;
                            $newWidth = (int) round(($width / $height) * $maxWidth);
                        }

                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        // Preserve transparency
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);

                        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($image);
                        $image = $resized;
                    } else {
                        // Even if not downscaled, ensure alpha channel is preserved for PNG/WebP transparency
                        imagealphablending($image, false);
                        imagesavealpha($image, true);
                    }

                    // Buffer WebP output with compression quality
                    ob_start();
                    imagewebp($image, null, $quality);
                    $webpContent = ob_get_clean();

                    // Progressive compression loop if output is somehow still large (> 1.8MB)
                    $currentQuality = $quality;
                    while (strlen($webpContent) > 1.8 * 1024 * 1024 && $currentQuality > 30) {
                        $currentQuality -= 15;
                        ob_start();
                        imagewebp($image, null, $currentQuality);
                        $webpContent = ob_get_clean();
                    }

                    imagedestroy($image);

                    if (!empty($webpContent)) {
                        Storage::disk($disk)->put($fullPath, $webpContent);
                        return $fileName;
                    }
                }
            } catch (\Throwable $e) {
                Log::error('ImageOptimizer WebP conversion error: ' . $e->getMessage());
            }
        }

        // Fallback: Verify image validity before storing fallback
        $imageInfo = @getimagesizefromstring($rawBinary);
        if ($imageInfo !== false && !empty($imageInfo['mime'])) {
            $mime = $imageInfo['mime'];
            $ext = match ($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'webp',
            };

            $fallbackName = $filenameWithoutExt . '.' . $ext;
            $fallbackPath = $folderPath ? ($folderPath . '/' . $fallbackName) : $fallbackName;
            Storage::disk($disk)->put($fallbackPath, $rawBinary);

            return $fallbackName;
        }

        Log::warning('ImageOptimizer: Rejecting invalid non-image payload for ' . $filenameWithoutExt);
        throw new \InvalidArgumentException('Format berkas gambar tidak valid.');
    }
}
