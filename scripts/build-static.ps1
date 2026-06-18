# build-static.ps1
# Reads includes/*.html and inlines them into all HTML pages,
# replacing <div data-include="name"></div> placeholders with actual content.
#
# Usage: Run this script after editing any file in includes/
#        powershell -ExecutionPolicy Bypass -File scripts/build-static.ps1

$themeDir = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\themes\kopizon"
$includesDir = Join-Path $themeDir "includes"

# Load all include contents
$includeFiles = @{}
Get-ChildItem -Path $includesDir -Filter "*.html" | ForEach-Object {
    $name = $_.BaseName
    $content = Get-Content -Path $_.FullName -Raw
    $includeFiles[$name] = $content.TrimEnd()
    Write-Host "  Loaded include: $name ($($content.Length) chars)"
}

Write-Host "`nIncludes loaded: $($includeFiles.Count)"
Write-Host "---"

# Process each HTML page in the theme root
$htmlFiles = Get-ChildItem -Path $themeDir -Filter "*.html" -File
$updated = 0

foreach ($file in $htmlFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    $originalContent = $content
    $replacements = @()

    # Find all data-include placeholders and replace with actual content
    foreach ($name in $includeFiles.Keys) {
        $placeholder = '<div data-include="' + $name + '"></div>'
        if ($content -match [regex]::Escape($placeholder)) {
            $content = $content -replace [regex]::Escape($placeholder), $includeFiles[$name]
            $replacements += $name
        }
    }

    # Remove kopizon-includes.js reference (no longer needed at runtime)
    # Keep it commented so we know it was there
    $content = $content -replace '\s*<script src="assets/js/kopizon-includes\.js"></script>', ''

    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        $updated++
        Write-Host "Built: $($file.Name) [inlined: $($replacements -join ', ')]"
    }
    else {
        Write-Host "Unchanged: $($file.Name)"
    }
}

Write-Host "---"
Write-Host "Done! Updated $updated files."
Write-Host ""
Write-Host "TIP: To revert pages back to placeholders for future editing, run build-includes.ps1"
