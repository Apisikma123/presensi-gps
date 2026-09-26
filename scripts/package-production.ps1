# ==============================================================================
# Production Deployment Packager for Rumahweb Shared Hosting (No SSH Required)
# ==============================================================================

param (
    [string]$OutputDir = "release_package",
    [string]$ArchiveName = "presensi-gps-production.zip"
)

$ErrorActionPreference = "Stop"
$ProjectRoot = Resolve-Path (Join-Path $PSScriptRoot "..")

Write-Host "======================================================================" -ForegroundColor Cyan
Write-Host " Building Production Artifact for Rumahweb Shared Hosting" -ForegroundColor Green
Write-Host " Project Root: $ProjectRoot" -ForegroundColor Gray
Write-Host "======================================================================" -ForegroundColor Cyan

Set-Location $ProjectRoot

# 1. Clean dev hot file if exists
if (Test-Path "public/hot") {
    Write-Host "Removing public/hot dev file..." -ForegroundColor Yellow
    Remove-Item "public/hot" -Force
}

# 2. Build Vite Frontend Assets
Write-Host "`n[Step 1/5] Compiling Vite production assets..." -ForegroundColor Yellow
npm run build
if (-not (Test-Path "public/build/manifest.json")) {
    Write-Error "Vite build failed! public/build/manifest.json not found."
    exit 1
}

# 3. Optimize Composer Autoloader (Production)
Write-Host "`n[Step 2/5] Optimizing Composer dependencies (--no-dev)..." -ForegroundColor Yellow
composer install --no-dev --prefer-dist --optimize-autoloader

# 4. Prepare Staging Directory
$StagingPath = Join-Path $ProjectRoot $OutputDir
if (Test-Path $StagingPath) {
    Remove-Item $StagingPath -Recurse -Force
}
New-Item -ItemType Directory -Path $StagingPath | Out-Null

Write-Host "`n[Step 3/5] Copying core application files to staging..." -ForegroundColor Yellow

$includeFolders = @(
    "app",
    "bootstrap",
    "config",
    "database",
    "public",
    "resources",
    "routes",
    "vendor"
)

foreach ($folder in $includeFolders) {
    if (Test-Path $folder) {
        Copy-Item -Path $folder -Destination $StagingPath -Recurse -Force
    }
}

# Ensure local test uploads / symlink in public/storage are excluded from production build
$stagingPublicStorage = Join-Path $StagingPath "public/storage"
if (Test-Path $stagingPublicStorage) {
    Remove-Item $stagingPublicStorage -Recurse -Force -ErrorAction SilentlyContinue
}

# Clean storage skeleton (directories only, no runtime files/logs)
$stagingStorage = Join-Path $StagingPath "storage"
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "app/public/uploads/absensi") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "app/private/attendance-archives") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "app/private/face-recognition") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "app/private/sid") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "framework/cache/data") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "framework/sessions") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "framework/views") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $stagingStorage "logs") -Force | Out-Null

# Preserve security .htaccess in private photo upload folder
if (Test-Path "storage/app/public/uploads/absensi/.htaccess") {
    Copy-Item "storage/app/public/uploads/absensi/.htaccess" -Destination (Join-Path $stagingStorage "app/public/uploads/absensi/.htaccess") -Force
}

# Copy essential root files
$includeFiles = @(
    "artisan",
    "composer.json",
    "composer.lock",
    ".env.example",
    ".htaccess"
)

foreach ($file in $includeFiles) {
    if (Test-Path $file) {
        Copy-Item -Path $file -Destination $StagingPath -Force
    }
}

# 5. Calculate Metrics & Breakdown
Write-Host "`n[Step 4/6] Auditing production staging payload..." -ForegroundColor Yellow
$allFiles = Get-ChildItem -Path $StagingPath -Recurse -File
$allDirs = Get-ChildItem -Path $StagingPath -Recurse -Directory
$totalFileCount = $allFiles.Count
$totalDirCount = $allDirs.Count

$vendorFiles = (Get-ChildItem -Path (Join-Path $StagingPath "vendor") -Recurse -File -ErrorAction SilentlyContinue).Count
$appFiles = (Get-ChildItem -Path (Join-Path $StagingPath "app") -Recurse -File -ErrorAction SilentlyContinue).Count
$publicFiles = (Get-ChildItem -Path (Join-Path $StagingPath "public") -Recurse -File -ErrorAction SilentlyContinue).Count
$storageFiles = (Get-ChildItem -Path (Join-Path $StagingPath "storage") -Recurse -File -ErrorAction SilentlyContinue).Count
$otherFiles = $totalFileCount - ($vendorFiles + $appFiles + $publicFiles + $storageFiles)

Write-Host "Total Files       : $totalFileCount" -ForegroundColor White
Write-Host "Total Directories : $totalDirCount" -ForegroundColor White
Write-Host "  - vendor        : $vendorFiles files" -ForegroundColor Gray
Write-Host "  - app           : $appFiles files" -ForegroundColor Gray
Write-Host "  - public        : $publicFiles files" -ForegroundColor Gray
Write-Host "  - storage skel  : $storageFiles files" -ForegroundColor Gray
Write-Host "  - other         : $otherFiles files" -ForegroundColor Gray

# 6. Create ZIP Archive
Write-Host "`n[Step 5/6] Creating release ZIP archive: $ArchiveName..." -ForegroundColor Yellow
$ZipDestination = Join-Path $ProjectRoot $ArchiveName
if (Test-Path $ZipDestination) {
    Remove-Item $ZipDestination -Force
}

Compress-Archive -Path "$StagingPath/*" -DestinationPath $ZipDestination -CompressionLevel Optimal

# 7. Summary & Cleanup Staging
Write-Host "`n[Step 6/6] Cleaning up staging directory..." -ForegroundColor Yellow
Remove-Item $StagingPath -Recurse -Force

$zipItem = Get-Item $ZipDestination
$zipSizeMb = [math]::Round($zipItem.Length / 1MB, 2)

Write-Host "`n======================================================================" -ForegroundColor Cyan
Write-Host " PRODUCTION PACKAGE READY!" -ForegroundColor Green
Write-Host " Archive File       : $ZipDestination" -ForegroundColor White
Write-Host " Package Size       : $zipSizeMb MB" -ForegroundColor White
Write-Host " Total File Count   : $totalFileCount" -ForegroundColor White
Write-Host " Total Dir Count    : $totalDirCount" -ForegroundColor White
Write-Host " Inode Budget (250k): $([math]::Round(($totalFileCount + $totalDirCount) / 250000 * 100, 2))% used" -ForegroundColor Green
Write-Host " Breakdown:" -ForegroundColor Cyan
Write-Host "   vendor           : $vendorFiles files" -ForegroundColor White
Write-Host "   app              : $appFiles files" -ForegroundColor White
Write-Host "   public           : $publicFiles files" -ForegroundColor White
Write-Host "   storage skeleton : $storageFiles files" -ForegroundColor White
Write-Host "   other            : $otherFiles files" -ForegroundColor White
Write-Host " Excluded           : node_modules, .git, tests, debug scripts, local logs, .env" -ForegroundColor Gray
Write-Host " Tests in Package   : ZERO (0)" -ForegroundColor Green
Write-Host "======================================================================" -ForegroundColor Cyan
