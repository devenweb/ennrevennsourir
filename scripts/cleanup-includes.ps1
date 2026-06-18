# cleanup-includes.ps1
# Fixes issues from the build-includes conversion:
# 1. Remove orphan </div> after topbar placeholder
# 2. Fix duplicate kopizon-features.js references
# 3. Ensure correct script order: includes.js THEN features.js
# 4. Remove leftover <!-- Pink Top Bar --> comments before placeholders
# 5. Remove orphan kopizon-features.js that were injected before build

$themeDir = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\themes\kopizon"
$htmlFiles = Get-ChildItem -Path $themeDir -Filter "*.html" -File

foreach ($file in $htmlFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    $originalContent = $content
    
    # 1. Remove orphan </div> immediately after topbar placeholder
    $content = $content -replace '(<div data-include="topbar"></div>)\s*</div>', '$1'
    
    # 2. Remove orphan <!-- Pink Top Bar --> comments
    $content = $content -replace '\s*<!-- Pink Top Bar -->\s*(?=<div data-include)', "`r`n"
    
    # 3. Remove ALL individual kopizon-features.js and kopizon-includes.js script tags
    $content = $content -replace '\s*<script src="assets/js/kopizon-features\.js"></script>', ''
    $content = $content -replace '\s*<script src="assets/js/kopizon-includes\.js"></script>', ''
    
    # 4. Add the correct scripts ONCE in the right order before </body>
    $content = $content -replace '(\s*</body>)', "`r`n    <script src=`"assets/js/kopizon-includes.js`"></script>`r`n    <script src=`"assets/js/kopizon-features.js`"></script>`r`n`$1"
    
    # 5. Remove orphan empty lines (3+ consecutive blank lines → 2)
    $content = $content -replace "(\r?\n){4,}", "`r`n`r`n"
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Cleaned: $($file.Name)"
    }
    else {
        Write-Host "Clean: $($file.Name)"
    }
}

Write-Host "`nCleanup complete!"
