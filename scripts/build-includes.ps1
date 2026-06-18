# build-includes.ps1
# DEPRECATED: This script was used for static HTML include injection.
# The project has been fully converted to a WordPress theme.
# Use WordPress templates (header.php, footer.php, etc.) for changes.

$themeDir = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\themes\kopizon"
$htmlFiles = Get-ChildItem -Path $themeDir -Filter "*.html" -File

$topbarPlaceholder = '    <div data-include="mobile-drawer"></div>' + "`r`n" + '    <div data-include="topbar"></div>'
$headerPlaceholder = '    <div data-include="header"></div>'
$footerCtaPlaceholder = '    <div data-include="footer-cta"></div>'
$footerPlaceholder = '    <div data-include="footer"></div>'
$mobileDrawerPlaceholder = '    <div data-include="mobile-drawer"></div>'

foreach ($file in $htmlFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    $originalContent = $content
    $changes = @()

    # Skip files that already use data-include
    if ($content -match 'data-include=') {
        Write-Host "Skipped (already converted): $($file.Name)"
        continue
    }

    # 1. Replace page-loader + top bar block
    # Pattern: <!-- Page Loading Animation --> ... mockup-top-bar ... </div>\s*</div>\s*</div>
    # Or: header-top-bar-replica pattern
    if ($content -match '(?s)(\s*<!-- Page Loading Animation -->.*?<div class="kopizon-page-loading"[^>]*></div>\s*)') {
        $content = $content -replace '(?s)\s*<!-- Page Loading Animation -->.*?<div class="kopizon-page-loading"[^>]*></div>\s*', "`r`n"
        $changes += "removed page-loader (moved to mobile-drawer include)"
    }

    # Replace mockup-top-bar block
    # Match both <!-- Pink Top Bar --> and <!-- Top Bar -->
    if ($content -match '(?s)(\s*<!-- (Pink )?Top Bar -->.*?<div class="mockup-top-bar">.*?</div>\s*</div>\s*</div>)') {
        $content = $content -replace '(?s)\s*<!-- (Pink )?Top Bar -->.*?<div class="mockup-top-bar">.*?</div>\s*</div>\s*</div>', "`r`n$topbarPlaceholder"
        $changes += "replaced top bar"
    }
    # Also handle simpler mockup-top-bar if the above fails
    elseif ($content -match '(?s)(\s*<div class="mockup-top-bar">.*?</div>\s*</div>\s*</div>)') {
        $content = $content -replace '(?s)\s*<div class="mockup-top-bar">.*?</div>\s*</div>\s*</div>', "`r`n$topbarPlaceholder"
        $changes += "replaced top bar (no comment)"
    }
    # Also handle header-top-bar-replica (donation, contact, checkout, cart pages)
    elseif ($content -match '(?s)(\s*<div class="header-top-bar-replica".*?</div>\s*</div>)') {
        $content = $content -replace '(?s)\s*<div class="header-top-bar-replica"[^>]*>.*?</div>\s*</div>', "`r`n    <div data-include=`"topbar`"></div>"
        $changes += "replaced top bar (replica)"
    }

    # 2. Replace header block
    # Match: <header class="mockup-header"> ... </header>
    # Or: <header class="site-header mockup-header"> ... </header>
    if ($content -match '(?s)<header class="[^"]*mockup-header[^"]*">.*?</header>') {
        $content = $content -replace '(?s)\s*<header class="[^"]*mockup-header[^"]*">.*?</header>', "`r`n$headerPlaceholder"
        $changes += "replaced header"
    }

    # 3. Replace footer CTA block
    if ($content -match '(?s)<!-- Footer CTA.*?<div class="footer-cta-blue"[^>]*>.*?</div>\s*</div>') {
        $content = $content -replace '(?s)\s*<!-- Footer CTA.*?<div class="footer-cta-blue"[^>]*>.*?</div>\s*</div>', "`r`n$footerCtaPlaceholder"
        $changes += "replaced footer CTA"
    }
    elseif ($content -match '(?s)<div class="footer-cta-blue"[^>]*>.*?</div>\s*</div>') {
        $content = $content -replace '(?s)\s*<div class="footer-cta-blue"[^>]*>.*?</div>\s*</div>', "`r`n$footerCtaPlaceholder"
        $changes += "replaced footer CTA"
    }

    # 4. Replace deep footer block
    if ($content -match '(?s)<!-- Deep Footer -->\s*<footer class="deep-footer">') {
        $content = $content -replace '(?s)\s*<!-- Deep Footer -->\s*<footer class="deep-footer">.*?</footer>', "`r`n$footerPlaceholder"
        $changes += "replaced footer"
    }
    elseif ($content -match '(?s)<footer class="deep-footer">') {
        $content = $content -replace '(?s)\s*<footer class="deep-footer">.*?</footer>', "`r`n$footerPlaceholder"
        $changes += "replaced footer"
    }

    # 5. Replace mobile drawer + overlay + back-to-top block
    if ($content -match '(?s)<!-- Mobile Nav Overlay -->') {
        $content = $content -replace '(?s)\s*<!-- Mobile Nav Overlay -->.*?<!-- Back to Top -->\s*<button class="back-to-top"[^>]*>.*?</button>', ""
        $changes += "removed mobile drawer (now in include)"
    }

    # 6. Ensure both JS files are referenced, avoiding duplicates
    if ($content -notmatch 'kopizon-includes\.js') {
        $content = $content -replace '(</body>)', "    <script src=`"assets/js/kopizon-includes.js`"></script>`r`n`$1"
        $changes += "added kopizon-includes.js"
    }
    if ($content -notmatch 'kopizon-features\.js') {
        $content = $content -replace '(</body>)', "    <script src=`"assets/js/kopizon-features.js`"></script>`r`n`$1"
        $changes += "added kopizon-features.js"
    }

    # 7. Ensure index.css is linked
    if ($content -notmatch 'index\.css') {
        $content = $content -replace '(</head>)', "    <link rel=`"stylesheet`" href=`"index.css`">`r`n`$1"
        $changes += "added index.css"
    }

    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Converted: $($file.Name) [$($changes -join ', ')]"
    }
    else {
        Write-Host "No changes: $($file.Name)"
    }
}

Write-Host "`nBuild complete! All pages now use includes."
