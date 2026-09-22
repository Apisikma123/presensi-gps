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

# 5. Create ZIP Archive
Write-Host "`n[Step 4/5] Creating release ZIP archive: $ArchiveName..." -ForegroundColor Yellow
$ZipDestination = Join-Path $ProjectRoot $ArchiveName
if (Test-Path $ZipDestination) {
    Remove-Item $ZipDestination -Force
}

Compress-Archive -Path "$StagingPath/*" -DestinationPath $ZipDestination -CompressionLevel Optimal

# 6. Summary & Cleanup Staging
Remove-Item $StagingPath -Recurse -Force

$zipItem = Get-Item $ZipDestination
$zipSizeMb = [math]::Round($zipItem.Length / 1MB, 2)

Write-Host "`n======================================================================" -ForegroundColor Cyan
Write-Host " PRODUCTION PACKAGE READY!" -ForegroundColor Green
Write-Host " Archive File : $ZipDestination" -ForegroundColor White
Write-Host " Package Size : $zipSizeMb MB" -ForegroundColor White
Write-Host " Excluded     : node_modules, .git, tests, debug scripts, local logs, .env" -ForegroundColor Gray
Write-Host "======================================================================" -ForegroundColor Cyan
