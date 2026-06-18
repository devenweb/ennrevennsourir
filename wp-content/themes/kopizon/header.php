<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <!-- Page Loading Animation -->
    <div class="kopizon-page-loading" id="page-loader"></div>

    <!-- Mobile Nav Overlay -->
    <div class="mobile-nav-overlay" id="mobile-overlay"></div>

    <!-- Mobile Nav Drawer -->
    <div class="mobile-nav-drawer" id="mobile-drawer">
        <button class="close-drawer" id="close-drawer">&times;</button>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'mobile-menu-items',
            'fallback_cb'    => 'kopizon_mobile_menu_fallback',
        ) );
        ?>
    </div>

    <!-- Back to Top -->
    <button class="back-to-top" id="back-to-top" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>


    <div class="mockup-top-bar">
        <div class="container tb-container">
            <div class="tb-left">
                <div class="logo">
                    <?php
                    if ( has_custom_logo() ) {
                        the_custom_logo();
                    } else {
                        echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="custom-logo-link"><img src="' . get_template_directory_uri() . '/assets/images/logo-web.png" alt="' . get_bloginfo( 'name' ) . '" class="custom-logo" style="max-height: 40px;"></a>';
                    }
                    ?>
                </div>
                <div class="tb-divider"></div>
                <div class="tb-search">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <i class="fas fa-search"></i>
                        <input type="search" placeholder="Search here..." name="s" value="<?php echo get_search_query(); ?>">
                    </form>
                </div>
            </div>
            <div class="tb-center-social">
                <a href="<?php echo esc_url( get_theme_mod( 'kopizon_facebook', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
                <a href="<?php echo esc_url( get_theme_mod( 'kopizon_linkedin', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>
                <a href="<?php echo esc_url( get_theme_mod( 'kopizon_instagram', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                <a href="<?php echo esc_url( get_theme_mod( 'kopizon_youtube', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="tb-right">
                <div class="contact-block">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-text">
                        <span class="contact-label">Drop a line</span>
                        <a href="mailto:<?php echo esc_attr( get_theme_mod( 'kopizon_email', 'info@ennrevennsourir.org' ) ); ?>" class="contact-value" style="font-size: 14px;"><?php echo esc_html( get_theme_mod( 'kopizon_email', 'info@ennrevennsourir.org' ) ); ?></a>
                    </div>
                </div>
                <div class="contact-block">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-text">
                        <span class="contact-label">Call experts</span>
                        <a href="tel:<?php echo esc_attr( get_theme_mod( 'kopizon_phone', '+230 460 2500' ) ); ?>" class="contact-value" style="font-size: 14px;"><?php echo esc_html( get_theme_mod( 'kopizon_phone', '+230 460 2500' ) ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <header class="mockup-header">
        <div class="container header-container">
            <div class="logo header-sticky-logo">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="custom-logo-link"><img src="' . get_template_directory_uri() . '/assets/images/logo-web.png" alt="' . get_bloginfo( 'name' ) . '" class="custom-logo" style="max-height: 40px; filter: brightness(0) invert(1);"></a>';
                }
                ?>
            </div>
            <nav class="main-navigation">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-menu',
                    'fallback_cb'    => 'kopizon_primary_menu_fallback',
                ) );
                ?>
            </nav>
            <div class="header-right">
                <div class="user-actions">
                    <a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>" class="login-link">
                        <i class="far fa-user-circle"></i> Login / Register
                    </a>
                </div>
                <a href="<?php echo esc_url( home_url( '/donation' ) ); ?>" class="btn-donate-red">Donate Now</a>
            </div>
            <button class="hamburger-btn" id="hamburger-btn" aria-label="Open menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
