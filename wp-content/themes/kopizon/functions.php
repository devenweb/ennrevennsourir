<?php
/**
 * Kopizon functions and definitions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/** Theme Support */
function kopizon_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // Register Navigation Menus
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'kopizon' ),
        'footer'  => esc_html__( 'Footer Menu', 'kopizon' ),
    ) );

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    // Add support for core custom logo.
    add_theme_support( 'custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ) );

    // Add WooCommerce Support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'kopizon_setup' );

/** Enqueue scripts and styles */
function kopizon_scripts() {
    // Fonts
    wp_enqueue_style( 'kopizon-fonts', 'https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700;800&display=swap', array(), null );
    
    // FontAwesome
    wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/assets/css/fontawesome/css/all.min.css', array(), '5.15.4' );

    // Swiper
    wp_enqueue_style( 'swiper', get_template_directory_uri() . '/assets/js/swiper/swiper.min.css', array(), '8.0.0' );

    // Bootstrap
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css', array(), '4.5.2' );

    // Main Stylesheet
    wp_enqueue_style( 'kopizon-custom', get_template_directory_uri() . '/index.css', array('bootstrap'), '1.1.5' );

    // Scripts
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '4.5.2', true );
    wp_enqueue_script( 'swiper-js', get_template_directory_uri() . '/assets/js/swiper/swiper.min.js', array(), '8.0.0', true );
    wp_enqueue_script( 'kopizon-features', get_template_directory_uri() . '/assets/js/kopizon-features.js', array('jquery'), '1.0.0', true );

    // Pass data to JS
    $hero_data = array();
    for ($i = 1; $i <= 3; $i++) {
        $img = get_theme_mod("kopizon_hero_img_$i");
        $title = get_theme_mod("kopizon_hero_title_$i");
        if ($img) {
            $hero_data[] = array(
                'image' => $img,
                'title' => $title ? $title : ''
            );
        }
    }

    wp_localize_script( 'kopizon-features', 'kopizonData', array(
        'templateUrl' => get_template_directory_uri(),
        'heroSlides'  => !empty($hero_data) ? $hero_data : false
    ) );
}
add_action( 'wp_enqueue_scripts', 'kopizon_scripts' );

/** WooCommerce Compatibility */
if ( class_exists( 'WooCommerce' ) ) {
    require get_template_directory() . '/lib/woocommerce.php';
}

/** Elementor Compatibility */
function kopizon_add_elementor_support() {
	add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'kopizon_add_elementor_support' );
/** Fallback Menus */
function kopizon_footer_menu_fallback() {
    ?>
    <ul class="footer-links list-unstyled">
        <li class="mb-3"><a href="<?php echo esc_url( home_url( '/sponsor-child' ) ); ?>">Sponsor a Child</a></li>
        <li class="mb-3"><a href="<?php echo esc_url( home_url( '/volunteer' ) ); ?>">Become a volunteer</a></li>
        <li class="mb-3"><a href="<?php echo esc_url( home_url( '/become-member' ) ); ?>">Become a member</a></li>
        <li class="mb-3"><a href="<?php echo esc_url( home_url( '/story' ) ); ?>">Who We Are</a></li>
        <li class="mb-3"><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact Us</a></li>
        <li class="mb-3"><a href="#">Terms &amp; Conditions</a></li>
        <li><a href="#">Privacy Policy</a></li>
    </ul>
    <?php
}
function kopizon_primary_menu_fallback() {
    ?>
    <ul class="nav-menu">
        <li class="header-sticky-logo-li">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sticky-logo-link">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-web.png" alt="Logo" style="max-height: 45px;">
            </a>
        </li>
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
        <li class="menu-divider">|</li>
        <li>
            <a href="#">About Us <i class="fas fa-chevron-down dropdown-icon"></i></a>
            <ul class="sub-menu dropdown-menu">
                <li><a href="<?php echo esc_url( home_url( '/story' ) ); ?>">Our Story</a></li>
                <li><a href="<?php echo esc_url( home_url( '/team' ) ); ?>">Our Team</a></li>
                <li><a href="<?php echo esc_url( home_url( '/audit-reports' ) ); ?>">Transparency & Reports</a></li>
            </ul>
        </li>
        <li class="menu-divider">|</li>
        <li>
            <a href="#">Our Work <i class="fas fa-chevron-down dropdown-icon"></i></a>
            <ul class="sub-menu dropdown-menu">
                <li><a href="<?php echo esc_url( home_url( '/cancer-scheme' ) ); ?>">Child Cancer Scheme</a></li>
                <li><a href="<?php echo esc_url( home_url( '/cancer-care' ) ); ?>">Childhood Cancer Care</a></li>
                <li><a href="<?php echo esc_url( home_url( '/news' ) ); ?>">Success Stories</a></li>
            </ul>
        </li>
        <li class="menu-divider">|</li>
        <li>
            <a href="#" style="color: var(--kopizon-pink);">Support Us <i class="fas fa-chevron-down dropdown-icon"></i></a>
            <ul class="sub-menu dropdown-menu">
                <li><a href="<?php echo esc_url( home_url( '/sponsor-child' ) ); ?>">Sponsor a Child</a></li>
                <li><a href="<?php echo esc_url( home_url( '/become-sponsor' ) ); ?>">Become a Sponsor</a></li>
                <li><a href="<?php echo esc_url( home_url( '/volunteer' ) ); ?>">Volunteer</a></li>
                <li><a href="<?php echo esc_url( home_url( '/become-member' ) ); ?>">Become a Member</a></li>
            </ul>
        </li>
        <li class="menu-divider">|</li>
        <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact</a></li>
        <li class="menu-divider">|</li>
        <li class="lang-switcher">
            <a href="#">English <i class="fas fa-chevron-down dropdown-icon"></i></a>
            <ul class="sub-menu dropdown-menu">
                <li><a href="#">Français</a></li>
            </ul>
        </li>
    </ul>
    <?php
}

/**
 * Mobile Menu Fallback
 */
function kopizon_mobile_menu_fallback() {
    ?>
    <ul class="mobile-menu-items">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
        <li class="menu-item-has-children">
            <a href="#" class="has-submenu">About Us <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
            <ul class="sub-menu">
                <li><a href="<?php echo esc_url( home_url( '/story' ) ); ?>">Our Story</a></li>
                <li><a href="<?php echo esc_url( home_url( '/team' ) ); ?>">Our Team</a></li>
                <li><a href="<?php echo esc_url( home_url( '/audit-reports' ) ); ?>">Transparency & Reports</a></li>
            </ul>
        </li>
        <li class="menu-item-has-children">
            <a href="#" class="has-submenu">Our Work <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
            <ul class="sub-menu">
                <li><a href="<?php echo esc_url( home_url( '/cancer-scheme' ) ); ?>">Child Cancer Scheme</a></li>
                <li><a href="<?php echo esc_url( home_url( '/cancer-care' ) ); ?>">Childhood Cancer Care</a></li>
                <li><a href="<?php echo esc_url( home_url( '/news' ) ); ?>">Success Stories</a></li>
            </ul>
        </li>
        <li class="menu-item-has-children">
            <a href="#" class="has-submenu">Support Us <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
            <ul class="sub-menu">
                <li><a href="<?php echo esc_url( home_url( '/sponsor-child' ) ); ?>">Sponsor a Child</a></li>
                <li><a href="<?php echo esc_url( home_url( '/become-sponsor' ) ); ?>">Become a Sponsor</a></li>
                <li><a href="<?php echo esc_url( home_url( '/volunteer' ) ); ?>">Volunteer</a></li>
                <li><a href="<?php echo esc_url( home_url( '/become-member' ) ); ?>">Become a Member</a></li>
            </ul>
        </li>
        <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact</a></li>
        <li class="lang-switcher-mobile">
            <a href="#" class="has-submenu">English <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
            <ul class="sub-menu">
                <li><a href="#">Français</a></li>
            </ul>
        </li>
        <li class="mobile-cta-item"><a href="<?php echo esc_url( home_url( '/donation' ) ); ?>" class="btn-donate-mobile-inline">Donate Now</a></li>
    </ul>
    <?php
}

/**
 * Filter to append Register and Language Switcher to the primary menu
 */
function kopizon_append_menu_items( $items, $args ) {
    if ( $args->theme_location == 'primary' ) {
        // Log to identify source if needed
        // error_log('[Kopizon] Filtering menu items. Current items length: ' . strlen($items));

        // 1. CLEANUP: Remove any items that we want to replace with our structured ones
        // This handles cases where they might be in the WP menu already
        $replacements = array(
            'Who we are'   => 'About Us',
            'Get Involved' => 'Support Us',
            'Reports'      => 'Transparency',
            'News'         => 'Success Stories'
        );

        foreach ($replacements as $old => $new) {
            // Remove <li> blocks containing the old label
            $items = preg_replace('/<li[^>]*>.*?'.preg_quote($old).'.*?<\/li>/si', '', $items);
        }

        // 2. Ensure Sticky Logo is present (only once)
        if ( strpos( $items, 'header-sticky-logo-li' ) === false ) {
            $sticky_logo = '<li class="header-sticky-logo-li">
                <a href="' . esc_url( home_url( '/' ) ) . '" class="sticky-logo-link">
                    <img src="' . get_template_directory_uri() . '/assets/images/logo-web.png" alt="Logo" style="max-height: 45px;">
                </a>
            </li>';
            $items = $sticky_logo . $items;
        }

        // 3. Rebuild structure if missing
        // Home
        if ( strpos( $items, '>Home</a>' ) === false ) {
            $items = preg_replace('/(<li class="header-sticky-logo-li">.*?<\/li>)/s', '$1<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>', $items);
            if ( strpos( $items, '>Home</a>' ) === false ) {
                $items .= '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
            }
        }

        // About Us
        if ( strpos( $items, 'About Us' ) === false ) {
            $about_us = '<li><a href="#">About Us <i class="fas fa-chevron-down dropdown-icon"></i></a><ul class="sub-menu dropdown-menu"><li><a href="'.esc_url(home_url('/story')).'">Our Story</a></li><li><a href="'.esc_url(home_url('/team')).'">Our Team</a></li><li><a href="'.esc_url(home_url('/audit-reports')).'">Transparency & Reports</a></li></ul></li>';
            $items = preg_replace('/(>Home<\/a><\/li>)/', '$1' . $about_us, $items);
        }

        // Our Work
        if ( strpos( $items, 'Our Work' ) === false ) {
            $our_work = '<li><a href="#">Our Work <i class="fas fa-chevron-down dropdown-icon"></i></a><ul class="sub-menu dropdown-menu"><li><a href="'.esc_url(home_url('/cancer-scheme')).'">Child Cancer Scheme</a></li><li><a href="'.esc_url(home_url('/cancer-care')).'">Childhood Cancer Care</a></li><li><a href="'.esc_url(home_url('/news')).'">Success Stories</a></li></ul></li>';
            $items .= $our_work;
        }

        // Support Us
        if ( strpos( $items, 'Support Us' ) === false ) {
            $support_us = '<li><a href="#" style="color: var(--kopizon-pink);">Support Us <i class="fas fa-chevron-down dropdown-icon"></i></a><ul class="sub-menu dropdown-menu"><li><a href="'.esc_url(home_url('/sponsor-child')).'">Sponsor a Child</a></li><li><a href="'.esc_url(home_url('/become-sponsor')).'">Become a Sponsor</a></li><li><a href="'.esc_url(home_url('/volunteer')).'">Volunteer</a></li><li><a href="'.esc_url(home_url('/become-member')).'">Become a Member</a></li></ul></li>';
            $items .= $support_us;
        }

        // Contact
        if ( strpos( $items, '>Contact</a>' ) === false && strpos( $items, '>Contact Us</a>' ) === false ) {
            $items .= '<li><a href="' . esc_url( home_url( '/contact' ) ) . '">Contact</a></li>';
        }

    }
    return $items;
}
add_filter( 'wp_nav_menu_items', 'kopizon_append_menu_items', 10, 2 );

/** Libs & Seeding */
require get_template_directory() . '/lib/customizer.php';
require get_template_directory() . '/lib/content-seeder.php';

// Auto-seed content once
add_action('init', function() {
    if (!get_option('kopizon_content_seeded')) {
        if (function_exists('kopizon_seed_content')) {
            kopizon_seed_content();
            update_option('kopizon_content_seeded', time());
        }
    }

    // Temporary Uploads Audit Trigger: ?audit_uploads=secret_6969
    if (isset($_GET['audit_uploads']) && $_GET['audit_uploads'] === 'secret_6969') {
        global $wpdb;
        $upload_dir = wp_upload_dir()['basedir'];
        
        echo "<h3>--- Auditing Database Attachments ---</h3>";
        $attachments = $wpdb->get_results("SELECT post_title, guid FROM {$wpdb->posts} WHERE post_type = 'attachment'", ARRAY_A);
        $db_files = [];
        foreach ($attachments as $att) {
            $file_path = str_replace(get_site_url() . '/wp-content/uploads/', '', $att['guid']);
            $db_files[$file_path] = $att['post_title'];
        }
        echo "<p>Found " . count($db_files) . " attachments in database.</p>";

        echo "<h3>--- Scanning Uploads Directory ---</h3>";
        $all_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir));
        $file_hashes = [];
        $orphans = [];
        $total_files = 0;

        foreach ($all_files as $file) {
            if ($file->isDir()) continue;
            $total_files++;
            $full_path = $file->getPathname();
            $relative_path = str_replace($upload_dir . DIRECTORY_SEPARATOR, '', $full_path);
            $relative_path = str_replace(DIRECTORY_SEPARATOR, '/', $relative_path);

            $is_thumb = preg_match('/-\d+x\d+\.(jpg|jpeg|png|gif|webp)$/i', $relative_path);
            $base_file = preg_replace('/-\d+x\d+(\.(jpg|jpeg|png|gif|webp))$/i', '$1', $relative_path);

            if (!isset($db_files[$relative_path]) && !isset($db_files[$base_file])) {
                if (strpos($relative_path, 'elementor/') === 0 || strpos($relative_path, 'fonts/') === 0 || strpos($relative_path, 'maxmegamenu/') === 0) continue;
                $orphans[] = $relative_path;
            }

            if (!$is_thumb && !strpos($relative_path, 'elementor/')) {
                $hash = md5_file($full_path);
                $file_hashes[$hash][] = $relative_path;
            }
        }

        echo "<p>Total files scanned: $total_files</p>";
        echo "<p>Orphaned files identified: " . count($orphans) . "</p>";

        if (!empty($orphans)) {
            echo "<h4>ORPHANED FILES (Ready for deletion)</h4><ul>";
            foreach ($orphans as $o) echo "<li>$o</li>";
            echo "</ul>";
        }

        echo "<h4>DUPLICATE FILES</h4>";
        foreach ($file_hashes as $hash => $paths) {
            if (count($paths) > 1) {
                echo "<strong>Hash: $hash</strong><ul>";
                foreach ($paths as $p) echo "<li>$p</li>";
                echo "</ul>";
            }
        }
        die('Audit Complete.');
    }
});
