# ==============================================================================
# Isolated Production Package Builder for Presence (Shared Hosting / cPanel)
# ==============================================================================

$ErrorActionPreference = "Stop"
$ProjectRoot = (Get-Item $PSScriptRoot).Parent.FullName
$StagingRoot = Join-Path $ProjectRoot "build_package"
$ReleaseRoot = Join-Path $ProjectRoot "release_package"
$ArchiveName = "presence-shared-hosting-production.zip"
$ZipDestination = Join-Path $ReleaseRoot $ArchiveName

Write-Host "======================================================================"
Write-Host " Building Clean Production Deployment Package for Shared Hosting"
Write-Host " Source Repository: $ProjectRoot"
Write-Host " Staging Temp Dir : $StagingRoot"
Write-Host " Output Zip Path  : $ZipDestination"
Write-Host "======================================================================"

# 1. Clean staging and ensure release directory exists
if (Test-Path $StagingRoot) {
    Remove-Item $StagingRoot -Recurse -Force
}
New-Item -ItemType Directory -Path $StagingRoot | Out-Null

if (-not (Test-Path $ReleaseRoot)) {
    New-Item -ItemType Directory -Path $ReleaseRoot | Out-Null
}

$appStaging = Join-Path $StagingRoot "presence"
New-Item -ItemType Directory -Path $appStaging | Out-Null

# 2. Copy Core Application Folders
$coreFolders = @(
    "app",
    "bootstrap",
    "config",
    "database",
    "public",
    "resources",
    "routes",
    "vendor"
)

Write-Host "`n[1/6] Copying core application directories..."
foreach ($folder in $coreFolders) {
    $src = Join-Path $ProjectRoot $folder
    if (Test-Path $src) {
        Write-Host "  -> Copying $folder..."
        Copy-Item -Path $src -Destination (Join-Path $appStaging $folder) -Recurse -Force
    }
}

# 3. Clean and Structure Storage Directory
Write-Host "`n[2/6] Building clean storage directory skeleton..."
$storageStaging = Join-Path $appStaging "storage"
New-Item -ItemType Directory -Path (Join-Path $storageStaging "app/public/uploads/absensi") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "app/public/uploads/karyawan") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "app/private/uploads/sid") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "app/private/uploads/facerecognition") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "app/private/attendance-archive") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "framework/cache/data") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "framework/sessions") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "framework/views") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $storageStaging "logs") -Force | Out-Null

# Add .gitignore in skeleton folders
Set-Content -Path (Join-Path $storageStaging "framework/cache/.gitignore") -Value "*`n!.gitignore"
Set-Content -Path (Join-Path $storageStaging "framework/sessions/.gitignore") -Value "*`n!.gitignore"
Set-Content -Path (Join-Path $storageStaging "framework/views/.gitignore") -Value "*`n!.gitignore"
Set-Content -Path (Join-Path $storageStaging "logs/.gitignore") -Value "*`n!.gitignore"
Set-Content -Path (Join-Path $storageStaging "app/private/attendance-archive/index.json") -Value "{`n}`n"

# 4. Copy Security .htaccess files
Write-Host "`n[3/6] Preserving nested security .htaccess files..."
$htaccessSources = @(
    @{ Src = "$ProjectRoot/.htaccess"; Dest = "$appStaging/.htaccess" },
    @{ Src = "$ProjectRoot/public/.htaccess"; Dest = "$appStaging/public/.htaccess" },
    @{ Src = "$ProjectRoot/public/models/.htaccess"; Dest = "$appStaging/public/models/.htaccess" },
    @{ Src = "$ProjectRoot/storage/app/public/uploads/.htaccess"; Dest = "$storageStaging/app/public/uploads/.htaccess" },
    @{ Src = "$ProjectRoot/storage/app/public/uploads/absensi/.htaccess"; Dest = "$storageStaging/app/public/uploads/absensi/.htaccess" },
    @{ Src = "$ProjectRoot/storage/app/private/.htaccess"; Dest = "$storageStaging/app/private/.htaccess" }
)

foreach ($ht in $htaccessSources) {
    if (Test-Path $ht.Src) {
        $destRel = $ht.Dest.Substring($appStaging.Length)
        Write-Host "  [OK] Preserved: $destRel"
        Copy-Item -Path $ht.Src -Destination $ht.Dest -Force
    } else {
        Write-Host "  [WARN] Missing $($ht.Src)"
    }
}

# 5. Copy Essential Root Files
Write-Host "`n[4/6] Copying essential root files..."
$rootFiles = @(
    "artisan",
    "composer.json",
    "composer.lock",
    ".env.example",
    "VERSION"
)

foreach ($file in $rootFiles) {
    $src = Join-Path $ProjectRoot $file
    if (Test-Path $src) {
        Copy-Item -Path $src -Destination (Join-Path $appStaging $file) -Force
    }
}

# 6. Apply Strict Exclusions Inside Staging
Write-Host "`n[5/6] Stripping dev, test, cache, dump, and internal markdown files from package..."

# A. Remove public/hot if copied
if (Test-Path (Join-Path $appStaging "public/hot")) {
    Remove-Item (Join-Path $appStaging "public/hot") -Force
}

# B. Remove database SQL dumps
Get-ChildItem -Path (Join-Path $appStaging "database") -Filter "*.sql" | Remove-Item -Force

# C. Clear any bootstrap cache generated files (keep directory and .gitignore)
Get-ChildItem -Path (Join-Path $appStaging "bootstrap/cache") -Exclude ".gitignore" | Remove-Item -Recurse -Force

# D. Strip internal markdown files if any copied into staging
Get-ChildItem -Path $appStaging -Filter "*.md" | Where-Object { $_.Name -ne "README.md" } | Remove-Item -Force

# E. Verify no tests or node_modules or .git made it into staging
$prohibited = @("tests", "node_modules", ".git", ".github", "scratch")
foreach ($p in $prohibited) {
    $pPath = Join-Path $appStaging $p
    if (Test-Path $pPath) {
        Remove-Item $pPath -Recurse -Force
    }
}

# Verify no .env (only .env.example)
$envPath = Join-Path $appStaging ".env"
if (Test-Path $envPath) {
    Remove-Item $envPath -Force
}

# Count files before packaging
$fileCount = (Get-ChildItem -Path $appStaging -Recurse -File).Count

# 7. Compress to ZIP using high-speed native bsdtar
Write-Host "`n[6/6] Compressing to production ZIP: $ArchiveName..."
if (Test-Path $ZipDestination) {
    Remove-Item $ZipDestination -Force
}

tar.exe -c --format zip -f "$ZipDestination" -C "$appStaging" .

# 8. Measure Package
$zipItem = Get-Item $ZipDestination
$zipBytes = $zipItem.Length
$zipSizeMb = [math]::Round($zipBytes / 1MB, 2)

# Cleanup Staging
Remove-Item $StagingRoot -Recurse -Force

Write-Host "`n======================================================================"
Write-Host " PRODUCTION PACKAGE SUCCESSFULLY CREATED!"
Write-Host " Archive Path     : $ZipDestination"
Write-Host " Package Size     : $zipSizeMb MB ($zipBytes bytes)"
Write-Host " Packaged Files   : $fileCount files"
Write-Host " Excluded         : .git, .env, tests, node_modules, sql dumps, logs, internal md"
Write-Host " Retained         : app, bootstrap, config, database, public, resources, routes, vendor, .htaccess"
Write-Host "======================================================================"
