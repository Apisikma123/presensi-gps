<?php

/**
 * Laravel Local Development Server with High-Performance Static Asset Caching
 *
 * This file replaces Laravel's default server.php to provide aggressive caching
 * headers (Cache-Control, ETag, 304 Not Modified) for static assets (.css, .js, .woff2, images).
 * This eliminates the 4-second reload overhead caused by PHP's single-threaded server
 * re-serving 50 uncompressed asset files on every page navigation.
 */

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''
);

$filePath = $publicPath . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'map'   => 'application/json',
    ];

    if (isset($mimeTypes[$extension])) {
        $lastModified = filemtime($filePath);
        $fileSize = filesize($filePath);
        $etag = sprintf('"%x-%x"', $lastModified, $fileSize);

        header('Content-Type: ' . $mimeTypes[$extension]);
        header('Cache-Control: public, max-age=86400, stale-while-revalidate=604800');
        header('ETag: ' . $etag);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');

        // Check conditional headers for 304 Not Modified
        $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';

        if (
            ($ifNoneMatch && trim($ifNoneMatch) === $etag) ||
            ($ifModifiedSince && @strtotime($ifModifiedSince) >= $lastModified)
        ) {
            http_response_code(304);
            exit;
        }

        header('Content-Length: ' . $fileSize);
        readfile($filePath);
        exit;
    }

    return false;
}

require_once $publicPath . '/index.php';
