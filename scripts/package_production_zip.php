<?php

$startTime = microtime(true);
$sourceDir = realpath(__DIR__ . '/../build_package/presence');
$releaseDir = realpath(__DIR__ . '/..') . '/release_package';
$zipFile = $releaseDir . '/presence-shared-hosting-production.zip';

if (!$sourceDir || !is_dir($sourceDir)) {
    echo "Source directory build_package/presence does not exist!\n";
    exit(1);
}

if (!is_dir($releaseDir)) {
    mkdir($releaseDir, 0755, true);
}

if (file_exists($zipFile)) {
    @unlink($zipFile);
}

echo "======================================================================\n";
echo " Building Clean Production Deployment Package for Shared Hosting (PHP)\n";
echo " Source Directory : {$sourceDir}\n";
echo " Destination ZIP  : {$zipFile}\n";
echo "======================================================================\n\n";

$zip = new ZipArchive();
$openRes = $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

if ($openRes !== true) {
    echo "Failed to create ZIP archive: code {$openRes}\n";
    exit(1);
}

echo "Indexing and compressing files into ZIP...\n";

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$fileCount = 0;
$dirCount = 0;

foreach ($files as $file) {
    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($sourceDir) + 1);
    // Normalize path separators to forward slash for zip/linux/cPanel standard
    $zipEntryName = str_replace('\\', '/', $relativePath);

    if ($file->isDir()) {
        $zip->addEmptyDir($zipEntryName);
        $dirCount++;
    } elseif ($file->isFile()) {
        $zip->addFile($filePath, $zipEntryName);
        $fileCount++;
    }
}

echo "Finalizing and closing ZIP archive ({$fileCount} files, {$dirCount} directories)...\n";
$zip->close();

$duration = round(microtime(true) - $startTime, 2);
$zipSize = filesize($zipFile);
$zipSizeMb = round($zipSize / 1048576, 2);

echo "\n======================================================================\n";
echo " PRODUCTION PACKAGE SUCCESSFULLY CREATED!\n";
echo " Archive Path     : {$zipFile}\n";
echo " Package Size     : {$zipSizeMb} MB ({$zipSize} bytes)\n";
echo " Packaged Files   : {$fileCount} files across {$dirCount} directories\n";
echo " Build Duration   : {$duration} seconds\n";
echo "======================================================================\n";
