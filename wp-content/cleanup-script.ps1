# WordPress Cleanup Script
# Purpose: Clean up log files and old backup directories
# Date: 2026-01-29

Write-Host "=== WordPress Cleanup Script ===" -ForegroundColor Cyan
Write-Host ""

$baseDir = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content"

# 1. Clean up WooCommerce log files older than 30 days
Write-Host "1. Cleaning WooCommerce log files..." -ForegroundColor Yellow
$logDir = Join-Path $baseDir "uploads\wc-logs"
$cutoffDate = (Get-Date).AddDays(-30)

if (Test-Path $logDir) {
    $oldLogs = Get-ChildItem -Path $logDir -File | Where-Object { $_.LastWriteTime -lt $cutoffDate }
    
    if ($oldLogs) {
        Write-Host "   Found $($oldLogs.Count) log files older than 30 days" -ForegroundColor Gray
        $totalSize = ($oldLogs | Measure-Object -Property Length -Sum).Sum / 1MB
        Write-Host "   Total size: $([math]::Round($totalSize, 2)) MB" -ForegroundColor Gray
        
        $oldLogs | Remove-Item -Force
        Write-Host "   ✓ Deleted old log files" -ForegroundColor Green
    } else {
        Write-Host "   No old log files to delete" -ForegroundColor Gray
    }
} else {
    Write-Host "   Log directory not found" -ForegroundColor Red
}

Write-Host ""

# 2. Remove empty -old directories
Write-Host "2. Removing empty backup directories..." -ForegroundColor Yellow
$oldDirs = Get-ChildItem -Path $baseDir -Directory -Recurse -Filter "*-old" -ErrorAction SilentlyContinue

foreach ($dir in $oldDirs) {
    $fileCount = (Get-ChildItem -Path $dir.FullName -Recurse -File -ErrorAction SilentlyContinue).Count
    
    if ($fileCount -eq 0) {
        Write-Host "   Removing empty: $($dir.Name)" -ForegroundColor Gray
        Remove-Item -Path $dir.FullName -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "   ✓ Deleted $($dir.Name)" -ForegroundColor Green
    } else {
        Write-Host "   Skipping (has files): $($dir.Name) - $fileCount files" -ForegroundColor Yellow
    }
}

Write-Host ""

# 3. Clean up WordPress debug log if larger than 10MB
Write-Host "3. Checking WordPress debug log..." -ForegroundColor Yellow
$debugLog = Join-Path $baseDir "debug.log"

if (Test-Path $debugLog) {
    $logSize = (Get-Item $debugLog).Length / 1MB
    
    if ($logSize -gt 10) {
        Write-Host "   Debug log is $([math]::Round($logSize, 2)) MB" -ForegroundColor Gray
        
        # Archive the log
        $archiveName = "debug-$(Get-Date -Format 'yyyy-MM-dd-HHmmss').log"
        $archivePath = Join-Path $baseDir "logs-archive"
        
        if (!(Test-Path $archivePath)) {
            New-Item -ItemType Directory -Path $archivePath | Out-Null
        }
        
        Move-Item -Path $debugLog -Destination (Join-Path $archivePath $archiveName)
        Write-Host "   ✓ Archived to logs-archive/$archiveName" -ForegroundColor Green
    } else {
        Write-Host "   Debug log size OK: $([math]::Round($logSize, 2)) MB" -ForegroundColor Gray
    }
} else {
    Write-Host "   No debug.log found" -ForegroundColor Gray
}

Write-Host ""

# 4. Summary
Write-Host "=== Cleanup Summary ===" -ForegroundColor Cyan

$currentSize = (Get-ChildItem -Path $baseDir -Recurse -File -ErrorAction SilentlyContinue | Measure-Object -Property Length -Sum).Sum / 1GB
Write-Host "Current wp-content size: $([math]::Round($currentSize, 2)) GB" -ForegroundColor White

Write-Host ""
Write-Host "✓ Cleanup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Recommendations:" -ForegroundColor Yellow
Write-Host "  - Review UpdraftPlus settings to store backups remotely" -ForegroundColor Gray
Write-Host "  - Consider implementing a caching plugin" -ForegroundColor Gray
Write-Host "  - Run this script monthly to maintain cleanliness" -ForegroundColor Gray
