# inject-features.ps1
# Injects page loader, hamburger, mobile drawer, back-to-top, and JS into all Kopizon HTML pages

$themeDir = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\themes\kopizon"
$htmlFiles = Get-ChildItem -Path $themeDir -Filter "*.html" | Where-Object { $_.Name -ne "index.html" }

$mobileDrawer = @'
    <!-- Mobile Nav Overlay -->
    <div class="mobile-nav-overlay" id="mobile-overlay"></div>

    <!-- Mobile Nav Drawer -->
    <div class="mobile-nav-drawer" id="mobile-drawer">
        <button class="close-drawer" id="close-drawer">&times;</button>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li>
                <a href="#" class="has-submenu">Who we are <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
                <ul class="sub-menu">
                    <li><a href="story.html">Our Story</a></li>
                    <li><a href="team.html">Our Team</a></li>
                    <li><a href="financial-reports.html">Financial Reports</a></li>
                </ul>
            </li>
            <li><a href="news.html">News</a></li>
            <li>
                <a href="#" class="has-submenu">Get Involved <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
                <ul class="sub-menu">
                    <li><a href="sponsor-child.html">Sponsor a Child</a></li>
                    <li><a href="help-adolescent.html">Help an Adolescent</a></li>
                    <li><a href="support-adult.html">Support an Adult</a></li>
                    <li><a href="become-sponsor.html">Become a Sponsor</a></li>
                    <li><a href="become-member.html">Become a member</a></li>
                    <li><a href="volunteer.html">Become a volunteer</a></li>
                    <li><a href="cancer-scheme.html">Child Cancer Scheme</a></li>
                    <li><a href="cancer-care.html">Childhood Cancer Care</a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="has-submenu">Reports <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
                <ul class="sub-menu">
                    <li><a href="annual-reports.html">Annual Reports</a></li>
                    <li><a href="audit-reports.html">Audit Reports</a></li>
                </ul>
            </li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="register.html">Register</a></li>
            <li class="mobile-cta"><a href="donation.html">Donate Now</a></li>
        </ul>
    </div>

    <!-- Back to Top -->
    <button class="back-to-top" id="back-to-top" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>
'@

$hamburgerBtn = @'
            <!-- Hamburger Button (mobile only) -->
            <button class="hamburger-btn" id="hamburger-btn" aria-label="Open menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
'@

$pageLoader = '    <!-- Page Loading Animation -->' + "`r`n" + '    <div class="kopizon-page-loading" id="page-loader"></div>' + "`r`n"
$jsScript = '    <script src="assets/js/kopizon-features.js"></script>'

foreach ($file in $htmlFiles) {
    $content = Get-Content -Path $file.FullName -Raw
    $modified = $false
    
    # 1. Add index.css if missing
    if ($content -notmatch 'index\.css') {
        $content = $content -replace '(</head>)', "    <link rel=`"stylesheet`" href=`"index.css`">`r`n`$1"
        $modified = $true
    }
    
    # 2. Add page loader after <body...> tag if missing
    if ($content -notmatch 'page-loader') {
        $content = $content -replace '(<body[^>]*>)', "`$1`r`n$pageLoader"
        $modified = $true
    }
    
    # 3. Add hamburger button before closing header div (</div>\s*</header>)
    if ($content -notmatch 'hamburger-btn') {
        # Insert before the last </div> inside header
        $content = $content -replace '(</div>\s*</header>)', "$hamburgerBtn`r`n        `$1"
        $modified = $true
    }
    
    # 4. Add mobile drawer, overlay, back-to-top before </body>
    if ($content -notmatch 'mobile-drawer') {
        $content = $content -replace '(</body>)', "$mobileDrawer`r`n`r`n$jsScript`r`n`$1"
        $modified = $true
    }
    
    if ($modified) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Updated: $($file.Name)"
    }
    else {
        Write-Host "Skipped (already has features): $($file.Name)"
    }
}

Write-Host "`nDone! All pages updated."
