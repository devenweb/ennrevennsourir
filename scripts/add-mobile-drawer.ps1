# add-mobile-drawer.ps1
# Adds mobile-drawer data-include placeholder to pages missing it

$themeDir = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\themes\kopizon"
$htmlFiles = Get-ChildItem -Path $themeDir -Filter "*.html" -File | Where-Object { $_.Name -ne "index.html" }

foreach ($file in $htmlFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    if ($content -notmatch 'data-include="mobile-drawer"') {
        $content = $content -replace '(<body[^>]*>)', "`$1`r`n    <div data-include=`"mobile-drawer`"></div>"
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Added mobile-drawer: $($file.Name)"
    }
    else {
        Write-Host "Already has: $($file.Name)"
    }
}

Write-Host "`nDone!"
